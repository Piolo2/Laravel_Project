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
        // Logic moved from get_markers.php
        $profiles = Profile::has('user')->with(['user.skills'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('user_id', 'full_name', 'latitude', 'longitude', 'profile_picture') // Select only what's needed, plus relations
            ->get();

        $markers = $profiles->map(function ($profile) {
            return [
                'user_id' => $profile->user_id,
                'full_name' => $profile->full_name,
                'latitude' => (float) $profile->latitude,
                'longitude' => (float) $profile->longitude,
                'categories' => $profile->user->role === 'resident' ? 'Resident, Service Provider' : 'Service Seeker',
                'skills' => $profile->user->skills->pluck('name')->implode(', '),
                'profile_picture' => $profile->profile_picture,
            ];
        });

        return response()->json($markers);
    }
}
