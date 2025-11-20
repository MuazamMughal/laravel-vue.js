<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\brand;
use App\Models\brandInvites;
use App\Models\mentor;
use App\Models\User;
use App\Models\Skill;
use App\Models\brandCollaboratorsInvites;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Models\brandCategory;
use App\Models\mentorCategory;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        Session::put('url.intended', Session::get('url.intended', RouteServiceProvider::MAIN));
        $skills = Skill::all();
        $brandCategories = brandCategory::all();
        $mentorCategories = mentorCategory::all();
        return Inertia::render('Auth/Register', [
            'skills' => $skills,
            'brandCategories' => $brandCategories,
            'mentorCategories' => $mentorCategories
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $profile = $request->profile ?? '';
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'password' => ['required'],
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

        $user = $this->createUser($request, $profile);

        if ($profile === 'mentor') {
            $mentor = $this->creatementorProfile($user, $request);
            if ($request->has('skills')) {
                $skillIds = collect($request->skills)->pluck('id')->toArray();
                $mentor->skills()->sync($skillIds);
            }
        } elseif ($profile === 'brand') {
            $this->createbrandProfile($user, $request);
        }

        checkAndAddAsbrandCollaborator($request->email);

        event(new Registered($user));
        Auth::login($user);

        $redirectUrl = !empty($request->get('r')) 
            ? base64_decode($request->get('r'))
            : Session::get('url.intended', RouteServiceProvider::MAIN);

        return $request->ajax()
            ? response()->json(['redirect_url' => $redirectUrl])
            : redirect($redirectUrl);
    }

    private function createUser(Request $request, string $profile): User
    {
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $profile === 'viewer' ? 'viewer' : 'startup',
            'slug' => slug($request->name) . Str::random(6),
            'password' => Hash::make($request->password),
            'type' => $profile,
        ];

        if ($profile === 'mentor') {
            $profilePhotoPath = defaultImage("USER");
        
            if ($request->hasFile('profile_photo')) {
                $profilePhotoPath = storeImage($request->file('profile_photo'), 'mentors/profile') ?? $profilePhotoPath;
            } 

            $userData = array_merge($userData, [
                'linkedin_url' => $request->linkedin_url,
                'profile_photo' => $profilePhotoPath,
            ]);
        } elseif ($profile === 'brand' ) {
            $userData = array_merge($userData, [
                'company_website' => $request->website,
            ]);
        }

        return User::create($userData);
    }

    private function creatementorProfile(User $user, Request $request): mentor
    {
        return mentor::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'slug' => $user->slug,
            'profile_photo' => $user->profile_photo,
            'cover_photo' => null,
            'status' => 'draft',
            'mentor_category_id' => $request->category_id,
            'linked_in_url' => $request->linkedin_url,
        ]);
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