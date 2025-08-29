<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SocialiteController extends Controller
{
    public function googleLogin()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            // if($user)
            $findUser = User::where('email', $user->email)->first();
            if ($findUser) {
                Auth::login($findUser);
                return redirect()->route('dashboard')->with('success', 'Login Successfully.');
            } else {
                $user = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_verified' => 1,
                ]);
               $user->assignRole('admin'); // ✅ Assign default role

                Auth::login($user);
                return redirect()->route('dashboard')->with('success', 'Login Successfully.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
            // dd($e->getMessage());
        }
    }

    public function redirectToFacebookProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookProviderCallback()
    {
        try {
            $user = Socialite::driver('facebook')->user();
            $user = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'is_verified' => 1,
            ]);
            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'Login Successfully.');
        } catch (\Exception $e) {
            return redirect('/login')->withErrors('Unable to authenticate with Facebook.');
        }
    }
}
