<?php

namespace App\Http\Controllers;

use App\Http\Requests\InfluencerRequest;
use App\Models\Influencer;
use App\Models\SocialMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $influencer = $id ? Influencer::with('socialMedias')->findOrFail($id) : null;
        $socialMedias = SocialMedia::all();

        return view('influencers.form', compact('influencer', 'socialMedias'));
    }

    public function store(InfluencerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $image = null;

        if ($request->filled('image')) {
            $tempPath = 'public/temp/' . $data['image'];
            if (Storage::exists($tempPath)) {
                $newPath = 'public/auth/' . $data['image'];
                Storage::move($tempPath, $newPath);
                $image = $data['image'];
                Storage::delete($tempPath);
            }
        }

        $influencer = Influencer::create([
            'name' => $data['name'],
            'image' => $image ?? null,
        ]);
        $socialMediaIds = $data['socialMedias'] ?? [];;
        $prices = $data['prices'] ?? [];
        foreach ($socialMediaIds as $socialMediaId) {
            $socialMedia = SocialMedia::find($socialMediaId);
            if ($socialMedia) {
                $influencer->socialMedias()->attach($socialMedia->id, [
                    'price' => $prices[$socialMediaId] ?? 0,
                ]);
            }
        }
        return redirect()->route('influencer.index')->with('success', 'Influencer created successfully!');
    }

    public function update(InfluencerRequest $request, $id): RedirectResponse
    {
        $data = $request->validated();

        $influencer = Influencer::findOrFail($id);

        $influencer->update([
            'name' => $data['name'],
            'image' => $data['image'] ?? $influencer->image,
        ]);

        $socialMediaIds = $data['socialMedias'];
        $prices = $data['prices'];

        $influencer->socialMedias()->detach();

        foreach ($socialMediaIds as $socialMediaId) {
            $socialMedia = SocialMedia::find($socialMediaId);

            if ($socialMedia) {
                $influencer->socialMedias()->attach($socialMedia->id, [
                    'price' => $prices[$socialMediaId] ?? 0,
                ]);
            }
        }

        return redirect()->route('influencer.index')->with('success', 'Influencer updated successfully!');
    }

    public function delete($id)
    {
        $influencer = Influencer::findOrFail($id);

        if ($influencer->image && Storage::exists('public/auth/' . $influencer->profile_img)) {
            Storage::delete('public/auth/' . $influencer->image);
        }

        $influencer->delete();
        return redirect()->route('influencer.index')->with('success', 'User deleted successfully');
    }

    public function uploadImage(Request $request, $id) {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();

        $previousImage = $request->input('previous_image');
        if ($previousImage && Storage::exists('public/temp/' . $previousImage)) {
            Storage::delete('public/temp/' . $previousImage);
        }
        $file->storeAs('public/temp', $filename);
        return response()->json(['file_path' => $filename]);
    }

    public function deleteImage(Request $request) {
        $filename = $request->input('filename');
        if ($filename && Storage::exists('public/temp/' . $filename)) {
            Storage::delete('public/temp/' . $filename);
            return response()->json(['success' => true, 'message' => 'Image deleted']);
        }
        return response()->json(['success' => false, 'message' => 'File not found'], 404);
    }

    public function cancel()
    {
        if (Storage::exists('public/temp')) {
            Storage::deleteDirectory('public/temp');
        }
        return redirect()->route('user.index');
    }
}
