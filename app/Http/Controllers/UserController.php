<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UserCreateNotification;
use App\Notifications\UserUpdateNotification;
use App\Notifications\UserDeleteNotification;
use Spatie\Permission\Models\Role;
use App\Traits\SendsNotifications;

class UserController extends Controller
{
    use SendsNotifications;
    public function index() {
        $user = auth()->user();
        
        if ($user->hasRole('super_admin')) {
            $users = User::all();
        } elseif ($user->hasRole('admin')) {
            // Admin sees their own sellers + themselves
            if (\Schema::hasColumn('users', 'created_by')) {
                $users = User::where('id', $user->id)
                    ->orWhere('created_by', $user->id)
                    ->get();
            } else {
                $users = User::where('id', $user->id)->get();
            }
        } else {
            // Sellers only see themselves
            $users = User::where('id', $user->id)->get();
        }
        
        return view('admin.users.index', compact('users'));
    }

    public function create() {
        $users = User::all();
        return view('admin.users.create', compact('users'));
    }

    public function store(Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'phone_number' => ['required', 'max:20'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'min:6'], // ✅ Add password validation
    ]);

    $userData = [
        'name' => $request->name,
        'email' => $request->email,
        'phone_number' => $request->phone_number,
        'password' => Hash::make($request->password),
    ];
    
    // Only add created_by if column exists
    if (\Schema::hasColumn('users', 'created_by')) {
        $userData['created_by'] = auth()->user()->hasRole('admin') ? auth()->id() : null;
    }
    
    $user = User::create($userData);

    // Assign default role based on who is creating the user
    if (auth()->user()->hasRole('admin')) {
        $user->assignRole('seller');
    } else {
        $user->assignRole('admin');
    }

    // ✅ Notify all users
    $this->sendNotificationToAll(UserCreateNotification::class, $user);

    return redirect()->route('users')->with('success', 'User created successfully!');
}

    public function edit($id){
        $users = User::where('id',$id)->first();
        return view('admin.users.edit', compact('users'));
    }
    public function update(Request $request)
    {

        $user = User::find($request->id);

        if ($user) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->password = Hash::make($request->password);

            $user->save();
            $this->sendNotificationToAll(UserUpdateNotification::class, $user);
            return redirect()->route('users')->with('success', 'User Updated successfully.!');
        }
    }

    public function delete($id){
        $user = User::find($id);
        $this->sendNotificationToAll(UserDeleteNotification::class, $user);
        $user->delete();
        return redirect()->route('users')->with('error', 'User delete successfully.!');
    }
}
