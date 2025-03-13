<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FacebookController extends Controller
{
    // Redirect user to Facebook login
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    // Handle Facebook callback
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
            $user = User::updateOrCreate([
                'facebook_id' => $facebookUser->id,
            ], [
                'username' => $facebookUser->name,
                'email' => $facebookUser->email,
                'password' => bcrypt(uniqid()),
                'facebook_token' => $facebookUser->token,
            ]);

            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'You have successfully logged in with Facebook!');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Something went wrong!');
        }
    }
}
