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

        // query request with same status - use 'with' so it wont load slow (n+1)
        $active_requests_data = $user->serviceRequestsAsSeeker()
            ->with('provider.profile')
            ->whereIn('status', ['Accepted'])
            ->get();

        $pending_requests_data = $user->serviceRequestsAsSeeker()
            ->with('provider.profile')
            ->where('status', 'Pending')
            ->get();

        $history_requests_data = $user->serviceRequestsAsSeeker()
            ->with('provider.profile')
            ->whereIn('status', ['Completed', 'Declined', 'Cancelled'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Format data for view
        $active_requests = $this->formatRequests($active_requests_data, 'provider');
        $pending_requests = $this->formatRequests($pending_requests_data, 'provider');
        $history_requests = $this->formatRequests($history_requests_data, 'provider');

        return view('seeker_requests', compact('active_requests', 'pending_requests', 'history_requests'));
    }

    public function providerIndex()
    {
        $user = Auth::user();

        $pending_requests_data = $user->serviceRequestsAsProvider()
            ->with('seeker.profile')
            ->where('status', 'Pending')
            ->get();

        $ongoing_requests_data = $user->serviceRequestsAsProvider()
            ->with('seeker.profile')
            ->where('status', 'Accepted')
            ->get();

        $history_requests_data = $user->serviceRequestsAsProvider()
            ->with('seeker.profile')
            ->whereIn('status', ['Declined', 'Completed', 'Cancelled'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pending_requests = $this->formatRequests($pending_requests_data, 'seeker');
        $ongoing_requests = $this->formatRequests($ongoing_requests_data, 'seeker');
        $history_requests = $this->formatRequests($history_requests_data, 'seeker');

        return view('provider_requests', compact('pending_requests', 'ongoing_requests', 'history_requests'));
    }

    /**
     * function to fix the data request format with name and contact.
     * i make this manual because its confusing the relationship of user and profile.
     */
    private function formatRequests($requests, $relation)
    {
        $formatted = [];
        // foreach request to put the name/address correct
        foreach ($requests as $req) {
            $user = $req->$relation;

            if ($user && $user->profile) {
                $req->{$relation . '_name'} = $user->profile->full_name;
                $req->{$relation . '_contact'} = $user->profile->contact_number;
                if ($relation === 'seeker') {
                    $req->seeker_address = $user->profile->address;
                }
            } else {
                $req->{$relation . '_name'} = $user->username ?? 'Unknown';
                $req->{$relation . '_contact'} = 'N/A';
                if ($relation === 'seeker') {
                    $req->seeker_address = 'N/A';
                }
            }
            $formatted[] = $req;
        }
        return $formatted;
    }

    public function store(Request $request)
    {
        $request->validate([
            'provider_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    // validations: check if provider is really a 'resident' role
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

        $serviceRequest = ServiceRequest::findOrFail($request->request_id);

        // Authorization check
        if ($serviceRequest->provider_id !== Auth::id() && $serviceRequest->seeker_id !== Auth::id()) {
            abort(403);
        }

        // dont allow update if request is already done or cancel
        if (in_array($serviceRequest->status, ['Completed', 'Declined', 'Cancelled'])) {
            $msg = 'Cannot update a finalized request.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg])
                : back()->with('error', $msg);
        }

        $serviceRequest->status = $request->status;
        $serviceRequest->save();

        $msg = "Request status updated to {$request->status}.";
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('msg', $msg);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:service_requests,id',
        ]);

        $deleted = ServiceRequest::whereIn('id', $request->ids)
            ->where('seeker_id', Auth::id())
            ->whereIn('status', ['Completed', 'Declined', 'Cancelled'])
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted . ' request(s) deleted successfully.'
        ]);
    }
}
