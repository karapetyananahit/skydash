<?php
namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function form($id = null)
    {
        $user = $id ? User::findOrFail($id) : null;
        return view('users.form', compact('user'));
    }

    public function store(UserRequest $request): RedirectResponse
    {
        User::create([
            'username' => $request->username,
            'country' => $request->country,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->has('is_admin') ? 'admin' : 'user',
            'profile_img' => $request->filled('profile_img') ? $request->profile_img : null,
        ]);

        return redirect()->route('user.index')->with('status', 'User created successfully!');
    }

    public function update(UserRequest $request, $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $updateData = [
            'username' => $request->username,
            'country' => $request->country,
            'email' => $request->email,
            'role' => $request->has('is_admin') ? 'admin' : 'user',
            'profile_img' => $request->filled('profile_img') ? $request->profile_img : $user->profile_img,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('user.index')->with('status', 'User updated successfully!');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User deleted successfully');
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
