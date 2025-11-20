<?php

namespace App\Http\Controllers;

use App\Models\brand;
use App\Models\Skill;
use App\Models\User;
use App\Models\mentor;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use App\Models\brandCategory;
use App\Models\mentorCategory;
use Illuminate\Support\Str;

class SocialRegisterController extends Controller
{
    public function index(): Response
    {
        $skills = Skill::all();
        $brandCategories = brandCategory::all();
        $mentorCategories = mentorCategory::all();
        return Inertia::render('Auth/Register', [
            'skills' => $skills,
            'brandCategories' => $brandCategories,
            'mentorCategories' => $mentorCategories
        ]);
    }

    public function store(Request $request)
    {
        $profile = $request->profile ?? '';
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'profile' => 'required|in:mentor,viewer,brand',
        ];

        if ($profile === 'mentor') {
            $rules = array_merge($rules, [
                'category_id' => ['required'],
                'linkedin_url' => 'required|string|' . url_pattern(),
            ]);
        } elseif ($profile === 'brand') {
            $rules = array_merge($rules, [
                'category_id' => 'required',
                'state_id' => 'required',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'cover_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        }

        $request->validate($rules);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $this->updateUser($user, $request, $profile);
            
            if ($profile === 'mentor') {
                $this->updatementorProfile($user, $request);
            } elseif ($profile === 'brand') {
                $this->updatebrandProfile($user, $request);
            }
        }

        $redirectUrl = !empty($request->get('r')) 
            ? base64_decode($request->get('r'))
            : Session::get('url.intended', RouteServiceProvider::MAIN);

        return $request->ajax()
            ? response()->json(['redirect_url' => $redirectUrl])
            : redirect($redirectUrl);
    }

    private function updateUser(User $user, Request $request, string $profile): void
    {
        $userData = [
            'name' => $request->name,
            'profile_photo' => $request->profile_photo ?? $user->profile_photo,
            'role' => $profile === 'viewer' ? 'viewer' : 'startup',
        ];

       if ($profile === 'mentor') {
            $userData['linkedin_url'] = $request->linkedin_url;
            if ($request->hasFile('profile_photo')) {
                $userData['profile_photo'] = storeImage($request->file('profile_photo'), 'mentors/profile');
            } else {
                $userData['profile_photo'] = $user->profile_photo ?? defaultImage('USER');
            }
        } elseif ($profile === 'brand') {
            $userData['company_website'] = $request->website;
        }

        $user->update($userData);
    }

    private function updatementorProfile(User $user, Request $request): void
    {
        $mentor = mentor::firstOrNew(['user_id' => $user->id]);
        $mentor->fill([
            'mentor_category_id' => $request->category_id,
            'linked_in_url' => $request->linkedin_url,
        ]);

        if (!$mentor->exists) {
            $mentor->uuid = Str::uuid();
            $mentor->slug = $user->slug;
            $mentor->status = 'draft';
        }
        $mentor->profile_photo = $user->profile_photo;

        $mentor->save();

        if ($request->has('skills')) {
            $skillIds = collect($request->skills)->pluck('id')->toArray();
            $mentor->skills()->sync($skillIds);
        }
    }


    private function updatebrandProfile(User $user, Request $request): void
    {
        $brand = brand::where('user_id', $user->id)->first();

        $brandData = [
            'name' => $request->brand_name ?? $user->name,
            'state_id' => $request->state_id,
            'category_id' => $request->category_id,
        ];

        if ($request->hasFile('logo')) {
            $brandData['poster'] = storeImage($request->file('logo'), 'brands/logos') 
                ?? $brand->poster;
        }

        if ($request->hasFile('cover_photo')) {
            $brandData['cover_photo'] = storeImage($request->file('cover_photo'), 'brands/covers')
                ?? $brand->cover_photo;
        }

        if ($brand) {
            $brand->update($brandData);
        } else {
            $this->createbrandProfile($user, $request);
        }
    }

    private function createbrandProfile(User $user, Request $request): void
    {
        $slug = Str::slug(substr($request->name, 0, 25));
        if (brand::where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }
        
        $logoPath = defaultImage("brand_LOGO");
        $coverPhotoPath = defaultImage("brand_COVER_PHOTO");

        if ($request->hasFile('logo')) {
            $logoPath = storeImage($request->file('logo'), 'brands/logos') ?? $logoPath;
        }

        if ($request->hasFile('cover_photo')) {
            $coverPhotoPath = storeImage($request->file('cover_photo'), 'brands/covers') ?? $coverPhotoPath;
        }

        brand::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'name' => $request->brand_name ?? $request->name,
            'slug' => $slug,
            'state_id' => $request->state_id,
            'poster' => $logoPath,
            'cover_photo' => $coverPhotoPath,
            'category_id' => $request->category_id,
        ]);
    }
}