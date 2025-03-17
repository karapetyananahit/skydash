<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        $facebookUser = Socialite::driver('facebook')->stateless()->user();

//        $facebookUser = Socialite::driver('facebook')->user();

        $user = User::where('email', $facebookUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'username' => $facebookUser->getName(),
                'email' => $facebookUser->getEmail(),
                'facebook_id' => $facebookUser->getId(),
                'avatar' => $facebookUser->getAvatar(),
                'password' => bcrypt('password'),
            ]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');

    }
}
