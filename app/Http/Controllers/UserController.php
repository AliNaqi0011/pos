<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UserCreateNotification;
use App\Notifications\UserUpdateNotification;
use App\Notifications\UserDeleteNotification;

use App\Traits\SendsNotifications;

class UserController extends Controller
{
    use SendsNotifications;
    public function index() {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        if ($user->hasRole('super_admin')) {
            // Super admin sees all users
            $users = User::with('roles')->get();
        } elseif ($user->hasRole('admin')) {
            // Admin sees users they created
            $users = User::with('roles')->where('created_by', $user->id)->get();
        } else {
            // Other roles cannot manage users
            abort(403, 'Access denied.');
        }
        
        return view('admin.users.index', compact('users'));
    }

    public function create() {
        $user = auth()->user();
        
        // Only super_admin and admin can create users
        if (!in_array($user->role, ['super_admin', 'admin'])) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }
        
        // Super admin creates admins, Admin creates sellers
        $allowedRole = $user->role === 'super_admin' ? 'admin' : 'seller';
        
        return view('admin.users.create', compact('allowedRole'));
    }

    public function store(Request $request) {
        $currentUser = auth()->user();
        
        // Check permissions
        if (!$currentUser->hasAnyRole(['super_admin', 'admin'])) {
            abort(403, 'Access denied.');
        }
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'min:6'],
            'role' => ['nullable', 'string', 'in:admin,seller,manager,sales']
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password' => Hash::make($validated['password']),
            'created_by' => $currentUser->id,
        ];
        
        $user = User::create($userData);
        
        // Assign role based on who is creating the user
        $roleToAssign = 'seller'; // default
        if ($currentUser->hasRole('super_admin')) {
            $roleToAssign = $validated['role'] ?? 'admin';
        } elseif ($currentUser->hasRole('admin')) {
            $roleToAssign = $validated['role'] ?? 'seller';
        }
        
        // Set both Spatie role and role column
        $user->assignRole($roleToAssign);
        $user->role = $roleToAssign;
        $user->save();

        try {
            $this->sendNotificationToAll(UserCreateNotification::class, $user);
        } catch (\Exception $e) {
            \Log::warning('Failed to send user notification: ' . $e->getMessage());
        }

        return redirect()->route('users')->with('success', 'User created successfully!');
    }

    public function edit($id){
        $currentUser = auth()->user();
        $user = User::find($id);
        
        if (!$user) {
            return redirect()->route('users')->with('error', 'User not found.');
        }
        
        // Check if current user can edit this user
        if ($user->created_by !== $currentUser->id) {
            return redirect()->route('users')->with('error', 'You can only edit users you created.');
        }
        
        return view('admin.users.edit', compact('user'));
    }
    public function update(Request $request)
    {
        $currentUser = auth()->user();
        $user = User::find($request->id);

        if (!$user) {
            return redirect()->route('users')->with('error', 'User not found.');
        }
        
        // Check if current user can update this user
        if ($user->created_by !== $currentUser->id) {
            return redirect()->route('users')->with('error', 'You can only update users you created.');
        }
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $request->id],
            'password' => ['nullable', 'min:6'],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        $this->sendNotificationToAll(UserUpdateNotification::class, $user);
        return redirect()->route('users')->with('success', 'User Updated successfully!');
    }

    public function delete($id){
        $currentUser = auth()->user();
        $user = User::find($id);
        
        if (!$user) {
            return redirect()->route('users')->with('error', 'User not found.');
        }
        
        // Check if current user can delete this user
        if ($user->created_by !== $currentUser->id) {
            return redirect()->route('users')->with('error', 'You can only delete users you created.');
        }
        
        $this->sendNotificationToAll(UserDeleteNotification::class, $user);
        $user->delete();
        return redirect()->route('users')->with('success', 'User deleted successfully!');
    }
}
