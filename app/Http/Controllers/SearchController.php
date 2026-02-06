<?php

namespace App\Http\Controllers;

use App\Models\SkillCategory;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index()
    {
        $categories = SkillCategory::all();
        $userLat = '';
        $userLng = '';

        if (Auth::check()) {
            $profile = Auth::user()->profile;
            if ($profile) {
                $userLat = $profile->latitude;
                $userLng = $profile->longitude;
            }
        }

        return view('search', compact('categories', 'userLat', 'userLng'));
    }

    public function getMarkers()
    {
        // Get raw profiles with relations
        $profiles = Profile::has('user')->with(['user.skills'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('user_id', 'full_name', 'latitude', 'longitude', 'profile_picture')
            ->get();

        $markers = [];
        foreach ($profiles as $profile) {
            // Determine user role label
            if ($profile->user->role === 'resident') {
                $roleLabel = 'Resident, Service Provider';
            } else {
                $roleLabel = 'Service Seeker';
            }

            // Manually build skills string
            $skillNames = [];
            foreach ($profile->user->skills as $skill) {
                $skillNames[] = $skill->name;
            }
            // Join skills with comma
            $skillsString = implode(', ', $skillNames);

            $markers[] = [
                'user_id' => $profile->user_id,
                'full_name' => $profile->full_name,
                'latitude' => (float) $profile->latitude,
                'longitude' => (float) $profile->longitude,
                'categories' => $roleLabel,
                'skills' => $skillsString,
                'profile_picture' => $profile->profile_picture,
            ];
        }

        return response()->json($markers);
    }
}
