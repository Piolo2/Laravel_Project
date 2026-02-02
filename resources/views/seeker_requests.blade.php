@extends('layouts.app')

@section('title', 'My Service Requests | Uni-Serve')

@section('content')
    <div class="container requests-container">
        <h2 style="color: var(--primary-color); margin-bottom: 2rem;">My Service Requests</h2>

        <div id="alert-container">
            @if (session('msg'))
                <div class="alert alert-success">{{ session('msg') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>

        <!-- Ongoing Requests -->
        <div style="margin-top: 30px;">
            <h3 style="color: var(--primary-color); font-size: 1.4rem;"><i class="fas fa-hammer"
                    style="color: var(--accent-blue);"></i> Ongoing / Accepted Requests</h3>

            @if (count($active_requests) > 0)
                <div style="margin-top: 15px;">
                    @foreach ($active_requests as $req)
                        <div class="request-card accepted" id="request-{{ $req->id }}">
                            <div class="request-header">
                                <h4 class="request-title">Service with {{ $req->provider_name }}</h4>
                                <span class="badge badge-accepted">Accepted</span>
                            </div>
                            <div class="request-meta">
                                <i class="fas fa-calendar-alt"></i> <strong>Date:</strong>
                                {{ \Carbon\Carbon::parse($req->service_date)->format('F j, Y, g:i a') }}
                            </div>
                            <div class="request-meta">
                                <i class="fas fa-phone"></i> <strong>Contact:</strong>
                                {{ $req->provider_contact }}
                            </div>
                            @if (!empty($req->notes))
                                <div class="request-notes">
                                    <strong>Your Note:</strong><br>
                                    {!! nl2br(e($req->notes)) !!}
                                </div>
                            @endif
                            <div class="request-actions">
                                <button type="button" class="btn btn-accent btn-sm"
                                    onclick="updateStatus({{ $req->id }}, 'Completed')">
                                    <i class="fas fa-check"></i> Done
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                    onclick="updateStatus({{ $req->id }}, 'Cancelled')">
                                    <i class="fas fa-times"></i> Not Completed
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p
                    style="padding: 40px; background: #fff; border-radius: 8px; border: 1px dashed #ccc; text-align:center; color:#888;">
                    No active requests at the moment.</p>
            @endif
        </div>

        <!-- Pending Requests -->
        <div style="margin-top: 40px;">
            <h3 style="color: var(--primary-color); font-size: 1.4rem;"><i class="fas fa-clock" style="color: #ffc107;"></i>
                Pending Requests</h3>
            @if (count($pending_requests) > 0)
                <div style="margin-top: 15px;">
                    @foreach ($pending_requests as $req)
                        <div class="request-card pending" id="request-{{ $req->id }}">
                            <div class="request-header">
                                <h4 class="request-title">Service with {{ $req->provider_name }}</h4>
                                <span class="badge badge-pending">Waiting for response</span>
                            </div>
                            <div class="request-meta">
                                <i class="fas fa-calendar-alt"></i> <strong>Date:</strong>
                                {{ \Carbon\Carbon::parse($req->service_date)->format('F j, Y, g:i a') }}
                            </div>
                            <div class="request-actions">
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                    onclick="updateStatus({{ $req->id }}, 'Cancelled')">
                                    <i class="fas fa-times"></i> Cancel Request
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="padding: 20px; text-align:center; color:#888;">No pending requests.</p>
            @endif
        </div>

        <!-- History -->
        <div style="margin-top: 40px;">
            <h3 style="color: var(--primary-color); font-size: 1.4rem;"><i class="fas fa-history"
                    style="color: #6c757d;"></i> Past Services</h3>
            @if (count($history_requests) > 0)
                <div style="margin-top: 15px;">
                    @foreach ($history_requests as $h)
                        <div class="request-card history">
                            <div class="request-header">
                                <h4 class="request-title">Service with {{ $h->provider_name }}</h4>
                                <span
                                    class="badge {{ $h->status == 'Completed' ? 'badge-completed' : 'badge-cancelled' }}">{{ $h->status }}</span>
                            </div>
                            <div class="request-meta">
                                <i class="fas fa-calendar-alt"></i> <strong>Date:</strong>
                                {{ \Carbon\Carbon::parse($h->service_date)->format('M j, Y') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="padding: 20px; text-align:center; color:#888;">No history yet.</p>
            @endif
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="modal"
        style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content"
            style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 90%; max-width: 500px; border-radius: 8px;">
            <h2 style="color: var(--primary-color); margin-bottom: 20px;">Rate Service</h2>
            <form id="reviewForm" action="{{ route('reviews.store') }}" method="POST">
                @csrf
                <input type="hidden" name="service_request_id" id="review_request_id">

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom: 5px;">Rating:</label>
                    <div class="rating-stars" style="font-size: 24px; color: #ffc107; cursor: pointer;">
                        <i class="far fa-star star" data-value="1" onclick="setRating(1)"></i>
                        <i class="far fa-star star" data-value="2" onclick="setRating(2)"></i>
                        <i class="far fa-star star" data-value="3" onclick="setRating(3)"></i>
                        <i class="far fa-star star" data-value="4" onclick="setRating(4)"></i>
                        <i class="far fa-star star" data-value="5" onclick="setRating(5)"></i>
                    </div>
                    <input type="hidden" name="rating" id="ratingInput" required>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Comment (Optional):</label>
                    <textarea name="comment" class="form-control" rows="3"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                </div>

                <div style="text-align: right;">
                    <button type="button" class="btn btn-secondary" onclick="window.location.reload()"
                        style="margin-right: 10px; background: #6c757d; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">Skip</button>
                    <button type="submit" class="btn btn-primary"
                        style="background: var(--primary-color); color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">Submit
                        Review</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentRating = 0;

            function setRating(value) {
                currentRating = value;
                document.getElementById('ratingInput').value = value;
                const stars = document.querySelectorAll('.rating-stars .star');
                stars.forEach(star => {
                    const starVal = parseInt(star.getAttribute('data-value'));
                    if (starVal <= value) {
                        star.classList.remove('far');
                        star.classList.add('fas');
                    } else {
                        star.classList.remove('fas');
                        star.classList.add('far');
                    }
                });
            }

            function updateStatus(requestId, status) {
                if (!confirm(`Are you sure you want to mark this as ${status}?`)) return;

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
                            if (status === 'Completed') {
                                document.getElementById('review_request_id').value = requestId;
                                document.getElementById('reviewModal').style.display = 'block';
                            } else {
                                location.reload();
                            }
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