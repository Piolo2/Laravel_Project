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
        $user = Auth::user();
        $count = $user->accomplishments()->count();

        if ($count >= 7) {
            return back()->with('error', 'You can can only add up to 7 accomplishments.');
        }

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'caption' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('accomplishments', 'public');

            // Explicitly prepare data
            $accomplishmentData = [
                'image_path' => '/storage/' . $path,
                'caption' => $request->caption,
            ];

            $user->accomplishments()->create($accomplishmentData);

            return back()->with('success', 'Accomplishment added successfully!');
        }

        return back()->with('error', 'Please upload an image.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $accomplishment = $user->accomplishments()->findOrFail($id);

        // Explicitly handle file path
        $fullPath = $accomplishment->image_path;
        $relativePath = str_replace('/storage/', '', $fullPath);

        // Safety check before delete
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }

        $accomplishment->delete();

        return back()->with('success', 'Accomplishment removed successfully!');
    }
}
