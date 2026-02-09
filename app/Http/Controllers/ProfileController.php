<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\SkillCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    private $barangays = [
        'Almacen',
        'Balagtas',
        'Balanacan',
        'Bonifacio',
        'Bulo Ibaba',
        'Bulo Ilaya',
        'Burgos',
        'Cabulihan Ibaba',
        'Cabulihan Ilaya',
        'Caigdal',
        'F. de Jesus (Poblacion)',
        'General Luna',
        'Kalilayan Ibaba',
        'Kalilayan Ilaya',
        'Mabini',
        'Mairok Ibaba',
        'Mairok Ilaya',
        'Malvar',
        'Maputat',
        'Muliguin',
        'Pagaguasan',
        'Panaon Ibaba',
        'Panaon Ilaya',
        'Plaridel',
        'Poctol',
        'Punta',
        'R. Lapu-lapu (Poblacion)',
        'R. Magsaysay (Poblacion)',
        'Raja Soliman (Poblacion)',
        'Rizal Ibaba',
        'Rizal Ilaya',
        'San Roque',
        'Socorro',
        'Tagumpay',
        'Tubas',
        'Tubigan'
    ];

    public function edit()
    {
        $profile = Auth::user()->profile;
        if (!$profile) {
            $profile = Profile::create(['user_id' => Auth::id(), 'full_name' => Auth::user()->username]);
        }
        $barangays = $this->barangays;
        $accomplishments = Auth::user()->accomplishments()->latest()->get();
        // query if user is verified provider status
        $verification = \App\Models\ProviderVerification::where('user_id', Auth::id())->first();

        return view('profile', compact('profile', 'barangays', 'accomplishments', 'verification'));
    }

    public function update(Request $request)
    {
        $profile = Auth::user()->profile;

        $request->validate([
            'full_name' => 'required|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'address' => ['required', 'string', \Illuminate\Validation\Rule::in($this->barangays)],
            'bio' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // Manually build the data array
        $data = [];
        $data['full_name'] = $request->input('full_name');
        $data['contact_number'] = $request->input('contact_number');

        // Handle address format
        $selected_barangay = $request->input('address');
        $data['address'] = $selected_barangay . ', Unisan, Quezon';

        $data['bio'] = $request->input('bio');
        $data['latitude'] = $request->input('latitude');
        $data['longitude'] = $request->input('longitude');

        if ($request->hasFile('profile_picture')) {
            $request->validate([
                'profile_picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = '/storage/' . $path;
        }

        $profile->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function viewProfile($id)
    {
        $profile = Profile::with('user.providerVerification')->where('user_id', $id)->firstOrFail();

        // get all skills then filter passing ones
        $all_skills = $profile->user->skills;
        $skills = [];
        foreach ($all_skills as $skill) {
            if ($skill->pivot->availability_status === 'Available') {
                $skills[] = $skill->name;
            }
        }

        $reviews = $profile->user->reviewsReceived()->with('seeker.profile')->latest()->get();

        // compute average rating using loop
        $total_rating = 0;
        $reviewCount = 0;
        foreach ($reviews as $review) {
            $total_rating += $review->rating;
            $reviewCount++;
        }

        if ($reviewCount > 0) {
            $averageRating = $total_rating / $reviewCount;
        } else {
            $averageRating = 0;
        }

        $accomplishments = $profile->user->accomplishments()->latest()->get();

        return view('view_profile', [
            'profile' => $profile,
            'skills' => $skills, // pass simple array
            'provider_id' => $id,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'reviewCount' => $reviewCount,
            'accomplishments' => $accomplishments
        ]);
    }
}
