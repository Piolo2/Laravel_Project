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
        return view('profile', compact('profile', 'barangays', 'accomplishments'));
    }

    public function update(Request $request)
    {
        $profile = Auth::user()->profile;

        $data = $request->validate([
            'full_name' => 'required|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'address' => ['required', 'string', \Illuminate\Validation\Rule::in($this->barangays)],
            'bio' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // Append ", Unisan, Quezon" to the address for storage consistency if needed, 
        // but since we validate against the specific barangay name, we'll store just the barangay 
        // OR we can append it. Let's store the full address for clarity in other parts of the app.
        // Actually, the user asked to limit location TO Unisan. 
        // Storing "Barangay X, Unisan, Quezon" is better for map geocoding if we use it later.

        $data['address'] = $data['address'] . ', Unisan, Quezon';

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
        $skills = $profile->user->skills->filter(function ($skill) {
            return $skill->pivot->availability_status === 'Available';
        })->pluck('name');

        $reviews = $profile->user->reviewsReceived()->with('seeker.profile')->latest()->get();
        $averageRating = $reviews->avg('rating');
        $reviewCount = $reviews->count();

        $accomplishments = $profile->user->accomplishments()->latest()->get();

        return view('view_profile', [
            'profile' => $profile,
            'skills' => $skills,
            'provider_id' => $id,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'reviewCount' => $reviewCount,
            'accomplishments' => $accomplishments
        ]);
    }
}
