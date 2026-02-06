<?php

namespace App\Http\Controllers;

use App\Models\ProviderVerification;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // If already verified or has pending application, redirect or show status
        // For now, let's just show the form

        return view('provider.verify', [
            'user' => $user
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string',
            'contact_number' => 'required|string',
            'id_front_file' => 'required|image|max:5120', // 5MB max
            'id_back_file' => 'required|image|max:5120',
        ]);

        // Manually build the verification data array
        $verificationData = [];
        $verificationData['user_id'] = Auth::id();
        $verificationData['first_name'] = $request->first_name;
        $verificationData['last_name'] = $request->last_name;
        $verificationData['address'] = $request->address;
        $verificationData['contact_number'] = $request->contact_number;

        // Handle skill types safely
        if ($request->skill_types) {
            $verificationData['skill_types'] = json_encode($request->skill_types);
        } else {
            $verificationData['skill_types'] = json_encode([]);
        }

        // Handle File Uploads Explictly
        if ($request->hasFile('id_front_file')) {
            $path = $request->file('id_front_file')->store('verification_docs', 'public');
            $verificationData['id_front_file'] = $path;
        }

        if ($request->hasFile('id_back_file')) {
            $path = $request->file('id_back_file')->store('verification_docs', 'public');
            $verificationData['id_back_file'] = $path;
        }

        if ($request->hasFile('compliance_certificate_file')) {
            $path = $request->file('compliance_certificate_file')->store('verification_docs', 'public');
            $verificationData['compliance_certificate_file'] = $path;
        }

        // Create Verification Record
        ProviderVerification::create($verificationData);

        // Optionally update Profile if not exists
        $user = Auth::user();
        if (!$user->profile) {
            $profileData = [
                'user_id' => $user->id,
                'full_name' => $request->first_name . ' ' . $request->last_name,
                'address' => $request->address,
                'contact_number' => $request->contact_number,
            ];
            Profile::create($profileData);
        }

        return redirect()->route('profile')->with('success', 'Verification request submitted successfully! Please wait for admin approval.');
    }
}
