<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\UserCode;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Http\Controllers\MailController;
use Twilio\Rest\Client;

class VerificationController extends Controller
{
    public function verifyUser(Request $request)
    {
        $verification_code = \Illuminate\Support\Facades\Request::get('code');
        $user = User::where(['verification_code' => $verification_code])->first();
        if ($user != null) {
            $user->is_verified = 1;
            $user->save();
            // User::where('email', Auth::user()->email)->update([
            //     'is_verified' => 1,
            // ]);
            return redirect()->route('dashboard')->with('success', 'Your account is verified.!');
        } else {
            return redirect()->route('email.verification.notice')->with('error', 'This email is expired. Please resend Email!');
        }

        return redirect()->route('login')->with('error', 'Invalid verification code!');
    }

    public function emailVerifyUser(Request $request)
    {
        $user = Auth::user();
        if ($user->is_verified == 0) {
            return view('admin.emailVerifyUser');
        }
        return redirect()->route('dashboard')->with('success', 'Your account is verified.!');
    }

    public function resendEmailVerifyUser(Request $request)
    {
        $user = Auth::user();
        $user->verification_code = sha1(time());
        $user->save();
        if ($user != null) {
            MailController::sendSignupEmail($user->name, $user->email, $user->verification_code);
            return redirect()->back()->with('success', 'Please check email for verification link.');
        }
    }

    public function verification()
    {
        $userEmail = Session::get('user_email');
        $userPhone = Session::get('user_phone');
        // Session::forget(['user_phone', 'user_email']);
        return view('admin.email-verify', compact('userPhone', 'userEmail'));
    }

    public function VerifyEmaiLPhoneOtp(Request $request)
    {
        $userEmail = Session::get('user_email');
        $userPhone = Session::get('user_phone');

        $userCodeData = UserCode::where('email', $userEmail)->where('phone', $userPhone)->latest()->first();
        if ($userCodeData->otp == $request->email_otp && $userCodeData->code == $request->phone_otp) {
            $user = User::where('email', $userEmail)->latest()->first();
            $user->email_status = 'verified';

            $formattedDateTime = now()->format('Y-m-d H:i:s');
            $user->last_login = $formattedDateTime;
            $user->save();
            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'Login Sucessfully');
        } else {
            return redirect()->back()->with('error', 'Invalid Otp');
        }
    }

    public function two_fa_verification(Request $request)
    {
        return view('admin.two_fa_verification');
    }

    public function verify_two_fa_email_otp(Request $request)
    {
        if ($request->email_otp) {
            $userEmail = Session::get('user_email');
            $user = User::where('email', $userEmail)->latest()->first();
            if ($user->email_otp == $request->email_otp) {
                Session::forget('user_email');
                Session::forget('user_phone');
                Auth::login($user);
                return redirect()->route('dashboard')->with('success', 'Email OTP is Matched. Login Sucessfully');
            } else {
                return redirect()->back()->with('error', 'Please enter correct Otp');
            }
        } else {
            return redirect()->back()->with('error', 'Please enter Otp');
        }
    }

    public function two_fa_phone_verification()
    {
        return view('admin.two_fa_phone_verification');
    }

    public function verify_two_fa_phone_otp(Request $request)
    {
        if ($request->phone_otp) {
            $userEmail = Session::get('user_email');
            // $userPhone = Session::get('user_phone');
            $user = User::where('email', $userEmail)->latest()->first();
            if ($user->phone_otp == $request->phone_otp) {
                Session::forget('user_email');
                Session::forget('user_phone');
                Auth::login($user);
                return redirect()->route('dashboard')->with('success', 'Phone OTP is Matched. Login Sucessfully');
            } else {
                return redirect()->back()->with('error', 'Please enter correct Otp');
            }
        } else {
            return redirect()->back()->with('error', 'Please enter Otp');
        }
    }

    public function phone_verification(Request $request)
    {
        $otpmobilecode = mt_rand(1000, 9999);
        // $to = $request->phone_number;
        // $twilio = new Client(config('services.twilio.account_sid'), config('services.twilio.auth_token'));
        // $twilio->messages->create(
        //     '+' . $to,
        //     [
        //         'from' => config('services.twilio.from'),
        //         'body' => "Your otp for Phone verification is: $otpmobilecode",
        //     ]
        // );
        User::where('id', Auth::user()->id)->update([
            'phone_otp' => $otpmobilecode,
        ]);

        return response()->json([
            'status' => true,
            'message' => "Otp is sent on user number",
            'PhoneNumber' => $request->phone_number
        ], 200);
    }

    public function check_phone_verification(Request $request) {
        $phone_otp = $request->phone_otp;
        $user_phone_otp = Auth::user()->phone_otp;
        if($user_phone_otp == $phone_otp) {
            User::where('id', Auth::user()->id)->update([
                'phone_verified' => 1,
                'phone_number' => $request->PhoneNumber,
            ]);
            return response()->json([
                'status' => true,
                'message' => "Otp is matched",
                'PhoneNumber' => $request->phone_number
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => "Otp is in correct",
                'PhoneNumber' => $request->phone_number
            ], 200);
        }
    }
}
