<?php

namespace App\Http\Controllers;

use App\Models\Accomplishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AccomplishmentController extends Controller
{
    public function store(Request $request)
    {
        $count = Auth::user()->accomplishments()->count();
        if ($count >= 7) {
            return back()->with('error', 'You can can only add up to 7 accomplishments.');
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'caption' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('accomplishments', 'public');

            Auth::user()->accomplishments()->create([
                'image_path' => '/storage/' . $path,
                'caption' => $request->caption,
            ]);

            return back()->with('success', 'Accomplishment added successfully!');
        }

        return back()->with('error', 'Please upload an image.');
    }

    public function destroy($id)
    {
        $accomplishment = Auth::user()->accomplishments()->findOrFail($id);

        // Delete file from storage
        $relativePath = str_replace('/storage/', '', $accomplishment->image_path);
        Storage::disk('public')->delete($relativePath);

        $accomplishment->delete();

        return back()->with('success', 'Accomplishment removed successfully!');
    }
}
