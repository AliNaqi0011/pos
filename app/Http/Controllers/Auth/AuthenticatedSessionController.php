<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerification;
use App\Models\User;
use Twilio\Rest\Client;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */


    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        
        // Simple redirect to dashboard
        return redirect()->route('dashboard')->with('success', 'Login Successfully!');
    }

    public function storeWithPreviousCode(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        if (Auth::user()->email_status == 'verified') {
            $loggedInUser = Auth::user();
            if ($loggedInUser->userSettings && $loggedInUser->userSettings->two_fa_type == 'email') {
                $otp_code = random_int(100000, 999999);
                $otp = [
                    'email_otpCode' => $otp_code,
                ];
                // Send Email Verification OTP
                Mail::to($loggedInUser->email)->send(new EmailVerification($otp));
                User::where('email', Auth::user()->email)->update([
                    'email_otp' => $otp_code,
                ]);
                Session::put('user_email', $loggedInUser->email);
                Session::put('user_phone', $loggedInUser->phone_number);
                Auth::logout();
                return redirect()->route('two.fa.verification')->with('success', 'Verification Page');
            } else if ($loggedInUser->userSettings && $loggedInUser->userSettings->two_fa_type == 'phone') {
                // Send Phone Number Verification OTP
                $otpmobilecode = mt_rand(1000, 9999);
                $to = $loggedInUser->phone_number;

                // Use Twilio to send the OTP
                $twilio = new Client(config('services.twilio.account_sid'), config('services.twilio.auth_token'));
                $twilio->messages->create(
                    '+' . $to,
                    [
                        'from' => config('services.twilio.from'),
                        'body' => "Your 2 FA otp for login is: $otpmobilecode",
                    ]
                );
                Session::put('user_email', $loggedInUser->email);
                Session::put('user_phone', $loggedInUser->phone_number);
                User::where('email', Auth::user()->email)->update([
                    'phone_otp' => $otpmobilecode,
                ]);
                Auth::logout();
                return redirect()->route('two.fa.phone.verification')->with('success', 'Otp is sent to your mobile number. Please enter otp here!');
            }
            $currentDateTime = now();
            $formattedDateTime = now()->format('Y-m-d H:i:s');
            $loggedInUser->last_login = $formattedDateTime;
            $loggedInUser->save();
            return redirect()->route('dashboard')->with('success', 'Login Sucessfully');
            // return redirect()->intended(RouteServiceProvider::HOME);
        } else {
            Session::put('user_email', Auth::user()->email);
            Session::put('user_phone', Auth::user()->phone_number);
            Auth::logout();
            return redirect()->route('verification')->with('error', 'Your Email and Phone number are not verified. Please verify your number and email');
            // return redirect()->back()->with('error', 'Your email is not verified');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
