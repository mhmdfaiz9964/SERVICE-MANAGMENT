<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // List users with search and status filter
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name, email, or mobile
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('id','desc')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    // Show form to create a new user
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    // Store new user
    public function store(Request $request)
    {
        $request->validate([
            'first_name'=>'required|string|max:255',
            'last_name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'mobile'=>'nullable|string|max:20',
            'password'=>'required|string|min:6|confirmed',
            'status'=>'required|in:active,inactive',
            'role'=>'required|exists:roles,name',
        ]);

        $user = User::create([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'email'=>$request->email,
            'mobile'=>$request->mobile,
            'status'=>$request->status,
            'password'=>Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success','User created successfully.');
    }

    // Show form to edit user
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user','roles'));
    }

    // Update user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name'=>'required|string|max:255',
            'last_name'=>'required|string|max:255',
            'email'=>"required|email|unique:users,email,{$user->id}",
            'mobile'=>'nullable|string|max:20',
            'password'=>'nullable|string|min:6|confirmed',
            'status'=>'required|in:active,inactive',
            'role'=>'required|exists:roles,name',
        ]);

        $user->update([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'email'=>$request->email,
            'mobile'=>$request->mobile,
            'status'=>$request->status,
            'password'=>$request->password ? Hash::make($request->password) : $user->password,
        ]);

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success','User updated successfully.');
    }

    // Delete user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success','User deleted successfully.');
    }
}
