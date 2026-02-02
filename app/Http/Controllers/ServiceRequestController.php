<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceRequestController extends Controller
{
    public function seekerIndex()
    {
        $user = Auth::user();

        $active_requests = $user->serviceRequestsAsSeeker()
            ->with('provider.profile')
            ->whereIn('status', ['Accepted'])
            ->get()
            ->map(function ($req) {
                $req->provider_name = $req->provider->profile->full_name ?? $req->provider->username;
                $req->provider_contact = $req->provider->profile->contact_number ?? 'N/A';
                return $req;
            });

        $pending_requests = $user->serviceRequestsAsSeeker()
            ->with('provider.profile')
            ->where('status', 'Pending')
            ->get()
            ->map(function ($req) {
                $req->provider_name = $req->provider->profile->full_name ?? $req->provider->username;
                return $req;
            });

        $history_requests = $user->serviceRequestsAsSeeker()
            ->with('provider.profile')
            ->whereIn('status', ['Completed', 'Declined', 'Cancelled'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($req) {
                $req->provider_name = $req->provider->profile->full_name ?? $req->provider->username;
                return $req;
            });

        return view('seeker_requests', compact('active_requests', 'pending_requests', 'history_requests'));
    }

    public function providerIndex()
    {
        $user = Auth::user();
        $pending_requests = $user->serviceRequestsAsProvider()
            ->with('seeker.profile')
            ->where('status', 'Pending')
            ->get()
            ->map(function ($req) {
                $req->seeker_name = $req->seeker->profile->full_name ?? $req->seeker->username;
                $req->seeker_contact = $req->seeker->profile->contact_number ?? 'N/A';
                $req->seeker_address = $req->seeker->profile->address ?? 'N/A';
                return $req;
            });

        $ongoing_requests = $user->serviceRequestsAsProvider()
            ->with('seeker.profile')
            ->where('status', 'Accepted')
            ->get()
            ->map(function ($req) {
                $req->seeker_name = $req->seeker->profile->full_name ?? $req->seeker->username;
                $req->seeker_contact = $req->seeker->profile->contact_number ?? 'N/A';
                return $req;
            });

        $history_requests = $user->serviceRequestsAsProvider()
            ->with('seeker.profile')
            ->whereIn('status', ['Declined', 'Completed', 'Cancelled'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($req) {
                $req->seeker_name = $req->seeker->profile->full_name ?? $req->seeker->username;
                return $req;
            });

        return view('provider_requests', compact('pending_requests', 'ongoing_requests', 'history_requests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'provider_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $isResident = \App\Models\User::where('id', $value)->where('role', 'resident')->exists();
                    if (!$isResident) {
                        $fail('The selected provider is invalid.');
                    }
                },
            ],
            'service_date' => 'required',
            'notes' => 'nullable|string'
        ]);

        ServiceRequest::create([
            'seeker_id' => Auth::id(),
            'provider_id' => $request->provider_id,
            'service_date' => $request->service_date,
            'notes' => $request->notes,
            'status' => 'Pending'
        ]);

        return back()->with('success', 'Service request sent successfully!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:service_requests,id',
            'status' => 'required|in:Accepted,Declined,Completed,Cancelled'
        ]);

        $status = $request->status;
        // Removed legacy mapping that forced Cancelled to Declined to preserve user intent

        $serviceRequest = ServiceRequest::findOrFail($request->request_id);

        // Simple authorization check
        if ($serviceRequest->provider_id !== Auth::id() && $serviceRequest->seeker_id !== Auth::id()) {
            abort(403);
        }

        // State machine check
        $finalStates = ['Completed', 'Declined', 'Cancelled'];
        if (in_array($serviceRequest->status, $finalStates)) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cannot update a finalized request.']);
            }
            return back()->with('error', 'Cannot update a finalized request.');
        }

        $serviceRequest->update(['status' => $status]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "Request status updated to $status."]);
        }

        return back()->with('msg', "Request status updated to $status.");
    }
}
