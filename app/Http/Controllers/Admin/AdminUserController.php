<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['profile', 'providerVerification']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            if ($request->role === 'pending_verification') {
                $query->whereHas('providerVerification', function ($q) {
                    $q->where('status', 'pending');
                });
            } elseif ($request->role === 'verified_provider') {
                $query->whereHas('providerVerification', function ($q) {
                    $q->where('status', 'approved');
                });
            } elseif ($request->role) {
                $query->where('role', $request->input('role'));
            }
        }

        $users = $query->paginate(10);

        $pendingCount = \App\Models\ProviderVerification::where('status', 'pending')->count();

        return view('admin.users.index', compact('users', 'pendingCount'));
    }

    public function show($id)
    {
        $user = User::with('profile', 'userSkills.skill.category', 'providerVerification')->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => 'required|in:admin,seeker,resident',
        ]);

        $updateData = [];
        $updateData['role'] = $request->role;

        $user->update($updateData);

        return redirect()->back()->with('success', 'User role updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // dont delete self
        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function approveVerification($id)
    {
        $user = User::findOrFail($id);

        if ($user->providerVerification) {
            $verificationUpdate = [];
            $verificationUpdate['status'] = 'approved';
            $verificationUpdate['rejection_reason'] = null;

            $user->providerVerification->update($verificationUpdate);

            return redirect()->back()->with('success', 'Provider verified successfully.');
        }

        return redirect()->back()->with('error', 'No verification request found.');
    }

    public function rejectVerification(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        if ($user->providerVerification) {
            $verificationUpdate = [];
            $verificationUpdate['status'] = 'rejected';
            $verificationUpdate['rejection_reason'] = $request->rejection_reason;

            $user->providerVerification->update($verificationUpdate);

            return redirect()->back()->with('success', 'Verification request rejected.');
        }

        return redirect()->back()->with('error', 'No verification request found.');
    }
}
