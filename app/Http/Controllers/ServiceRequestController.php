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

        // Get active requests
        $active_requests_data = $user->serviceRequestsAsSeeker()
            ->with('provider.profile')
            ->whereIn('status', ['Accepted'])
            ->get();

        $active_requests = [];
        foreach ($active_requests_data as $req) {
            if ($req->provider && $req->provider->profile) {
                $req->provider_name = $req->provider->profile->full_name;
                $req->provider_contact = $req->provider->profile->contact_number;
            } else {
                $req->provider_name = $req->provider->username;
                $req->provider_contact = 'N/A';
            }
            $active_requests[] = $req;
        }

        // Get pending requests
        $pending_requests_data = $user->serviceRequestsAsSeeker()
            ->with('provider.profile')
            ->where('status', 'Pending')
            ->get();

        $pending_requests = [];
        foreach ($pending_requests_data as $req) {
            if ($req->provider && $req->provider->profile) {
                $req->provider_name = $req->provider->profile->full_name;
            } else {
                $req->provider_name = $req->provider->username;
            }
            $pending_requests[] = $req;
        }

        // Get history requests
        $history_requests_data = $user->serviceRequestsAsSeeker()
            ->with('provider.profile')
            ->whereIn('status', ['Completed', 'Declined', 'Cancelled'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $history_requests = [];
        foreach ($history_requests_data as $req) {
            if ($req->provider && $req->provider->profile) {
                $req->provider_name = $req->provider->profile->full_name;
            } else {
                $req->provider_name = $req->provider->username;
            }
            $history_requests[] = $req;
        }

        return view('seeker_requests', compact('active_requests', 'pending_requests', 'history_requests'));
    }

    public function providerIndex()
    {
        $user = Auth::user();

        // Get pending requests
        $pending_requests_data = $user->serviceRequestsAsProvider()
            ->with('seeker.profile')
            ->where('status', 'Pending')
            ->get();

        $pending_requests = [];
        foreach ($pending_requests_data as $req) {
            if ($req->seeker && $req->seeker->profile) {
                $req->seeker_name = $req->seeker->profile->full_name;
                $req->seeker_contact = $req->seeker->profile->contact_number;
                $req->seeker_address = $req->seeker->profile->address;
            } else {
                $req->seeker_name = $req->seeker->username;
                $req->seeker_contact = 'N/A';
                $req->seeker_address = 'N/A';
            }
            $pending_requests[] = $req;
        }

        // Get ongoing requests
        $ongoing_requests_data = $user->serviceRequestsAsProvider()
            ->with('seeker.profile')
            ->where('status', 'Accepted')
            ->get();

        $ongoing_requests = [];
        foreach ($ongoing_requests_data as $req) {
            if ($req->seeker && $req->seeker->profile) {
                $req->seeker_name = $req->seeker->profile->full_name;
                $req->seeker_contact = $req->seeker->profile->contact_number;
            } else {
                $req->seeker_name = $req->seeker->username;
                $req->seeker_contact = 'N/A';
            }
            $ongoing_requests[] = $req;
        }

        // Get history requests
        $history_requests_data = $user->serviceRequestsAsProvider()
            ->with('seeker.profile')
            ->whereIn('status', ['Declined', 'Completed', 'Cancelled'])
            ->orderBy('created_at', 'desc')
            ->get();

        $history_requests = [];
        foreach ($history_requests_data as $req) {
            if ($req->seeker && $req->seeker->profile) {
                $req->seeker_name = $req->seeker->profile->full_name;
            } else {
                $req->seeker_name = $req->seeker->username;
            }
            $history_requests[] = $req;
        }

        return view('provider_requests', compact('pending_requests', 'ongoing_requests', 'history_requests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'provider_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    // Check if the user is a resident
                    $isResident = \App\Models\User::where('id', $value)->where('role', 'resident')->exists();
                    if (!$isResident) {
                        $fail('The selected provider is invalid.');
                    }
                },
            ],
            'service_date' => 'required',
            'notes' => 'nullable|string'
        ]);

        $input = [
            'seeker_id' => Auth::id(),
            'provider_id' => $request->provider_id,
            'service_date' => $request->service_date,
            'notes' => $request->notes,
            'status' => 'Pending'
        ];

        ServiceRequest::create($input);

        return back()->with('success', 'Service request sent successfully!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'request_id' => 'required|exists:service_requests,id',
            'status' => 'required|in:Accepted,Declined,Completed,Cancelled'
        ]);

        $status = $request->status;
        $serviceRequest = ServiceRequest::findOrFail($request->request_id);

        // Check if user is allowed to update
        $provider_id = $serviceRequest->provider_id;
        $seeker_id = $serviceRequest->seeker_id;
        $current_user_id = Auth::id();

        if ($provider_id !== $current_user_id) {
            if ($seeker_id !== $current_user_id) {
                abort(403);
            }
        }

        // Check if request is already finished
        $is_completed = ($serviceRequest->status == 'Completed');
        $is_declined = ($serviceRequest->status == 'Declined');
        $is_cancelled = ($serviceRequest->status == 'Cancelled');

        if ($is_completed || $is_declined || $is_cancelled) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cannot update a finalized request.']);
            }
            return back()->with('error', 'Cannot update a finalized request.');
        }

        // Update status
        $serviceRequest->status = $status;
        $serviceRequest->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => "Request status updated to $status."]);
        }

        return back()->with('msg', "Request status updated to $status.");

    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:service_requests,id',
        ]);

        $user_id = Auth::id();

        // Ensure records belong to logged-in user to prevent unauthorized deletion
        $deleted = ServiceRequest::whereIn('id', $request->ids)
            ->where('seeker_id', $user_id)
            ->whereIn('status', ['Completed', 'Declined', 'Cancelled']) // Only allow deleting history items
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted . ' request(s) deleted successfully.'
        ]);
    }
}
