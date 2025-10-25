<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserSocial;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    public function index() {
        $userSocial = UserSocial::where('user_id', Auth::user()->id)->first();
        $user = Auth::user();
        return view('admin.profile.index', compact('user', 'userSocial'));
    }

    public function edit($id) {
        $user = User::find($id);
        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request, $id) {
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->address = $request->address;
        if($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->profile_title_name = $request->profile_title_name;

        if ($user->profile_image && $request->profile_image) {
            $imagePath = public_path('template/images/profile-images/' . $user->profile_image);

            // Check if the file exists before attempting to delete
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        if ($request->profile_image) {
            $request->validate([
                'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            
            $image = $request->profile_image;
            $allowedExtensions = ['jpeg', 'jpg', 'png', 'gif'];
            $extension = strtolower($image->getClientOriginalExtension());
            
            if (!in_array($extension, $allowedExtensions)) {
                return redirect()->back()->with('error', 'Invalid file type. Only JPEG, PNG, JPG, and GIF are allowed.');
            }
            
            $imageName = time() . '.' . $extension;
            $image->move(public_path('template/images/profile-images'), $imageName);
            $user->profile_image = $imageName;
        }
        $user->save();
        return redirect()->route('user.profile')->with('success', 'Profile updated successfully.');
    }

    public function SocialCreate() {
        return view('admin.profile.socialCreate');
    }

    public function SocialStore(Request $request) {
        $userSocial = new UserSocial();
        $userSocial->user_id = Auth::user()->id;
        $userSocial->facebook = $request->facebook;
        $userSocial->twitter = $request->twitter;
        $userSocial->github = $request->github;
        $userSocial->instagram = $request->instagram;
        $userSocial->website = $request->website;
        $userSocial->save();
        return redirect()->route('user.profile')->with('success', 'Social Profile created successfully.');
    }

    public function SocialEdit($id) {
        $userSocial = UserSocial::find($id);
        return view('admin.profile.socialedit', compact('userSocial'));
    }

    public function SocialUpdate(Request $request, $id) {
        $userSocial = UserSocial::find($id);
        $userSocial->user_id = Auth::user()->id;
        $userSocial->facebook = $request->facebook;
        $userSocial->twitter = $request->twitter;
        $userSocial->github = $request->github;
        $userSocial->instagram = $request->instagram;
        $userSocial->website = $request->website;
        $userSocial->save();
        return redirect()->route('user.profile')->with('success', 'Social Profile updated successfully.');
    }
}
