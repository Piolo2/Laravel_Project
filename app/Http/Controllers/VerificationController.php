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

        $data = $request->except(['_token', 'id_front_file', 'id_back_file', 'compliance_certificate_file', 'profile_picture']);
        $data['user_id'] = Auth::id();
        $data['skill_types'] = json_encode($request->skill_types ?? []);

        // Handle File Uploads
        if ($request->hasFile('id_front_file')) {
            $data['id_front_file'] = $request->file('id_front_file')->store('verification_docs', 'public');
        }

        if ($request->hasFile('id_back_file')) {
            $data['id_back_file'] = $request->file('id_back_file')->store('verification_docs', 'public');
        }

        if ($request->hasFile('compliance_certificate_file')) {
            $data['compliance_certificate_file'] = $request->file('compliance_certificate_file')->store('verification_docs', 'public');
        }

        // Create Verification Record
        ProviderVerification::create($data);

        // Optionally update Profile if not exists
        $profile = Auth::user()->profile;
        if (!$profile) {
            Profile::create([
                'user_id' => Auth::id(),
                'full_name' => $request->first_name . ' ' . $request->last_name,
                'address' => $request->address,
                'contact_number' => $request->contact_number,
            ]);
        }

        return redirect()->route('profile')->with('success', 'Verification request submitted successfully! Please wait for admin approval.');
    }
}
