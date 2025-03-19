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
        ]);

        return redirect('/users');
    }
    public function edit($id)
    {
        $user = User::find($id);
        return view('users.edit', compact('user'));
    }

    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect('/users')->with('success', 'User deleted successfully');
    }

}
