<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Skill;
use App\Models\User;
use App\Models\Expert;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use App\Models\ChannelCategory;
use App\Models\ExpertCategory;
use Illuminate\Support\Str;

class SocialRegisterController extends Controller
{
    public function index(): Response
    {
        $skills = Skill::all();
        $channelCategories = ChannelCategory::all();
        $expertCategories = ExpertCategory::all();
        return Inertia::render('Auth/Register', [
            'skills' => $skills,
            'channelCategories' => $channelCategories,
            'expertCategories' => $expertCategories
        ]);
    }

    public function store(Request $request)
    {
        $profile = $request->profile ?? '';
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
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

        $user = User::where('email', $request->email)->first();
