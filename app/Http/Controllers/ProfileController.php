<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());
        $image = $request->file('profile_img');
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        if ($request->has('delete_image') && $request->delete_image == "1") {
            if ($request->user()->profile_img) {
                Storage::delete('public/auth/' . $request->user()->profile_img);
                $request->user()->profile_img = null;
            }
        }
        if ($request->hasFile('profile_img')) {
            if ($request->user()->profile_img) {
                Storage::delete('public/auth/' . $request->user()->profile_img);
            }
            $imageName = time() . '.' . $request->profile_img->extension();
            $image->storeAs('public/auth', $imageName);

            $request->user()->profile_img = $imageName;
        }
        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function deleteImage(Request $request)
    {
        $user = auth()->user();

        if ($user->profile_img) {
            Storage::delete('public/auth/' . $user->profile_img);
            $user->profile_img = null;
            $user->save();

            return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'No image to delete'], 400);
    }
}
