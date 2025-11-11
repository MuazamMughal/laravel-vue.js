<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIsViewer
{
    
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Check if user has viewer role
        if ($user && $user->role === 'viewer') {
            return redirect("/home")->with('error', 'You do not have permission to access this page.');
        }