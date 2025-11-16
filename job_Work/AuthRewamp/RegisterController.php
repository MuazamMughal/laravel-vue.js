<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\ChannelInvites;
use App\Models\Expert;
use App\Models\User;
use App\Models\Skill;
use App\Models\ChannelCollaboratorsInvites;
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
use App\Models\ChannelCategory;
use App\Models\ExpertCategory;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        Session::put('url.intended', Session::get('url.intended', RouteServiceProvider::MAIN));
        $skills = Skill::all();
        $channelCategories = ChannelCategory::all();
        $expertCategories = ExpertCategory::all();
        return Inertia::render('Auth/Register', [
            'skills' => $skills,
            'channelCategories' => $channelCategories,
            'expertCategories' => $expertCategories
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
            'profile' => 'required|in:expert,viewer,channel',
        ];

        if ($profile === 'expert') {
            $rules = array_merge($rules, [
                'category_id' => ['required'],
                'linkedin_url' => 'required|string|' . url_pattern(),
            ]);
        } elseif ($profile === 'channel') {
            $rules = array_merge($rules, [
                'category_id' => 'required',
                'state_id' => 'required',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'cover_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        }

        $request->validate($rules);

        $user = $this->createUser($request, $profile);

        if ($profile === 'expert') {
            $expert = $this->createExpertProfile($user, $request);
            if ($request->has('skills')) {
                $skillIds = collect($request->skills)->pluck('id')->toArray();
                $expert->skills()->sync($skillIds);
            }
        } elseif ($profile === 'channel') {
            $this->createChannelProfile($user, $request);
        }

        checkAndAddAsChannelCollaborator($request->email);

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

        if ($profile === 'expert') {
            $profilePhotoPath = defaultImage("USER");
        
            if ($request->hasFile('profile_photo')) {
                $profilePhotoPath = storeImage($request->file('profile_photo'), 'experts/profile') ?? $profilePhotoPath;
            } 

            $userData = array_merge($userData, [
                'linkedin_url' => $request->linkedin_url,
                'profile_photo' => $profilePhotoPath,
            ]);
        } elseif ($profile === 'channel' ) {
            $userData = array_merge($userData, [
                'company_website' => $request->website,
            ]);
        }

        return User::create($userData);
    }

    private function createExpertProfile(User $user, Request $request): Expert
    {
        return Expert::create([
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'slug' => $user->slug,
            'profile_photo' => $user->profile_photo,
            'cover_photo' => null,
            'status' => 'draft',
            'expert_category_id' => $request->category_id,
            'linked_in_url' => $request->linkedin_url,
        ]);
    }
