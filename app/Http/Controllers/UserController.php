<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'country' => ['required', 'string'],
            'password' => ['required', Password::defaults()],
        ]);
        User::create([
            'username' => $request->username,
            'country' => $request->country,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->has('is_admin') ? 'admin' : 'user',
        ]);

        return redirect('/users');
    }

    public function edit($id)
    {
        $user = User::find($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'profile_img' => 'nullable|string'
        ]);

        $user = User::findOrFail($id);

        $updateData = [
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->has('is_admin') ? 'admin' : 'user',
            'profile_img' => $request->filled('profile_img') ? $request->profile_img : null,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $user->update($updateData);

        return redirect()->route('user.index')
            ->with('status', 'User updated successfully!');
    }

    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect('/users')->with('success', 'User deleted successfully');
    }

    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('public/auth', $filename);

        return response()->json(['file_path' => $filename]);
    }

}
