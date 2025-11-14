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
