<?php

namespace App\Http\Controllers;

use App\Http\Requests\InfluencerRequest;
use App\Http\Requests\UserRequest;
use App\Models\Influencer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class InfluencerController extends Controller

{
    public function index()
    {
        $influencers = Influencer::all();
        return view('influencers.index', compact('influencers'));
    }

    public function form($id = null)
    {
        $influencer = $id ? Influencer::findOrFail($id) : null;
        return view('influencers.form', compact('influencer'));
    }

    public function store(InfluencerRequest $request): RedirectResponse
    {
        $influencer = Influencer::create([
            'name' => $request->name,
            'image' => $request->image ?? null,
        ]);

        if (!empty($request->socialMedias)) {
            $influencer->socialMedias()->attach($request->socialMedias);
        }

        return redirect()->route('influencers.index')->with('success', 'Influencer created successfully!');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);

        if ($user->profile_img && Storage::exists('public/auth/' . $user->profile_img)) {
            Storage::delete('public/auth/' . $user->profile_img);
        }

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

        $file->storeAs('public/temp', $filename);

        return response()->json(['file_path' => $filename]);
    }

    public function cancel()
    {
        if (Storage::exists('public/temp')) {
            Storage::deleteDirectory('public/temp');
        }
        return redirect()->route('user.index');
    }
}
