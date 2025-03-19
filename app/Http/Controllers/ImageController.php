<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'image' => 'required|image|max:2048', // 2MB max size
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->store('public/images');
            return response()->json(['message' => 'Image uploaded successfully!', 'path' => $path]);
        }

        return response()->json(['message' => 'Image upload failed'], 400);
    }
}
