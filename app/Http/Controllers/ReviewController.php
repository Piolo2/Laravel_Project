<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'service_request_id' => 'required|exists:service_requests,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $serviceRequest = ServiceRequest::findOrFail($request->service_request_id);

        // Authorization: Only the seeker of this request can review
        if ($serviceRequest->seeker_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if already reviewed
        if ($serviceRequest->review) {
            return back()->with('error', 'You have already reviewed this service.');
        }

        // Ensure status is completed
        if ($serviceRequest->status !== 'Completed') {
            $serviceRequest->update(['status' => 'Completed']);
        }

        // Prepare data for review creation
        $reviewData = [
            'service_request_id' => $serviceRequest->id,
            'seeker_id' => Auth::id(),
            'provider_id' => $serviceRequest->provider_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ];

        Review::create($reviewData);

        return back()->with('success', 'Thank you for your feedback!');
    }
}
