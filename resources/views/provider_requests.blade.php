@extends('layouts.app')

@section('title', 'Service Requests | Uni-Serve')

@section('content')
    <div class="container requests-container">
        <h2 style="color: var(--primary-color); margin-bottom: 2rem;">Service Requests</h2>

        <div id="alert-container">
            @if (session('msg'))
                <div class="alert alert-success">{{ session('msg') }}</div>
            @endif
        </div>

        <!-- Pending Requests -->
        <div style="margin-top: 30px;">
            <h3 style="color: var(--primary-color); font-size: 1.4rem;"><i class="fas fa-inbox" style="color: #ffc107;"></i>
                Pending Requests</h3>

            @if (count($pending_requests) > 0)
                <div style="margin-top: 15px;">
                    @foreach ($pending_requests as $req)
                        <div class="request-card pending" id="request-{{ $req->id }}">
                            <div class="request-header">
                                <h4 class="request-title">Request from {{ $req->seeker_name }}</h4>
                                <span class="badge badge-pending">Action Required</span>
                            </div>
                            <div class="request-meta">
                                <i class="fas fa-calendar-alt"></i> <strong>Preferred Date:</strong>
                                {{ \Carbon\Carbon::parse($req->service_date)->format('F j, Y, g:i a') }}
                            </div>
                            <div class="request-meta">
                                <i class="fas fa-map-marker-alt"></i> <strong>Location:</strong>
                                {{ $req->seeker_address }}
                            </div>
                            <div class="request-meta">
                                <i class="fas fa-phone"></i> <strong>Contact:</strong>
                                {{ $req->seeker_contact }}
                            </div>
                            @if (!empty($req->notes))
                                <div class="request-notes">
                                    <strong>Problem/Notes:</strong><br>
                                    {!! nl2br(e($req->notes)) !!}
                                </div>
                            @endif

                            <div class="request-actions">
                                <button type="button" class="btn btn-accent btn-sm"
                                    onclick="handleAction({{ $req->id }}, 'Accepted')">
                                    <i class="fas fa-check"></i> Accept Request
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                    onclick="handleAction({{ $req->id }}, 'Declined')">
                                    <i class="fas fa-times"></i> Decline
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p
                    style="padding: 40px; background: #fff; border-radius: 8px; border: 1px dashed #ccc; text-align:center; color:#888;">
                    No pending requests at the moment.</p>
            @endif
        </div>

        <!-- Ongoing Requests -->
        <div style="margin-top: 40px;">
            <h3 style="color: var(--primary-color); font-size: 1.4rem;"><i class="fas fa-hammer"
                    style="color: #007bff;"></i>
                Accepted / Ongoing Services</h3>

            @if (count($ongoing_requests) > 0)
                <div style="margin-top: 15px;">
                    @foreach ($ongoing_requests as $req)
                        <div class="request-card accepted" id="request-{{ $req->id }}">
                            <div class="request-header">
                                <h4 class="request-title">Ongoing Service for {{ $req->seeker_name }}</h4>
                                <span class="badge badge-accepted">Accepted</span>
                            </div>
                            <div class="request-meta">
                                <i class="fas fa-calendar-alt"></i> <strong>Agreed Date:</strong>
                                {{ \Carbon\Carbon::parse($req->service_date)->format('F j, Y, g:i a') }}
                            </div>
                            <div class="request-meta">
                                <i class="fas fa-phone"></i> <strong>Contact Seeker:</strong>
                                {{ $req->seeker_contact }}
                            </div>
                            @if (!empty($req->notes))
                                <div class="request-notes">
                                    <strong>Notes:</strong><br>
                                    {!! nl2br(e($req->notes)) !!}
                                </div>
                            @endif
                            <div class="request-actions">
                                <p style="font-size: 0.9rem; color: #666; font-style: italic;">Wait for Seeker to mark as Done or
                                    update status if needed via Seeker contact.</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="padding: 20px; text-align:center; color:#888;">No ongoing services.</p>
            @endif
        </div>

        <!-- History -->
        <div style="margin-top: 50px;">
            <h3 style="color: var(--primary-color); font-size: 1.4rem;"><i class="fas fa-history"
                    style="color: #6c757d;"></i> Recent History</h3>

            @if (count($history_requests) > 0)
                <div class="table-responsive" style="margin-top: 15px;">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Seeker</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($history_requests as $h)
                                <tr>
                                    <td style="font-weight:600; color: var(--primary-color);">
                                        {{ $h->seeker_name }}
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($h->service_date)->format('M j, Y') }}</td>
                                    <td>
                                        @php
                                            $sClass = 'badge-cancelled';
                                            if ($h->status == 'Accepted')
                                                $sClass = 'badge-accepted';
                                            if ($h->status == 'Declined')
                                                $sClass = 'badge-declined';
                                            if ($h->status == 'Completed')
                                                $sClass = 'badge-completed';
                                        @endphp
                                        <span class="badge {{ $sClass }}">{{ $h->status }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color:#888; padding: 20px; text-align:center;">No history yet.</p>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function handleAction(requestId, status) {
                if (!confirm(`Are you sure you want to ${status.toLowerCase()} this request?`)) return;

                fetch('{{ route('requests.update') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        request_id: requestId,
                        status: status
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Something went wrong.');
                    });
            }
        </script>
    @endpush
@endsection