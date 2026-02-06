@extends('layouts.app')

@section('title', 'My Service Requests | Uni-Serve')

@section('content')
    <div class="container requests-container">
        <div class="section-title text-center" style="margin-bottom: 3rem;">
            <h2 style="font-weight: 800; color: var(--primary-color);">My Service Requests</h2>
            <p class="text-muted">Manage your service appointments and history</p>
        </div>

        <div id="alert-container">
            @if (session('msg'))
                <div class="alert alert-success shadow-sm border-0">{{ session('msg') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger shadow-sm border-0">{{ session('error') }}</div>
            @endif
        </div>

        <!-- Ongoing / Accepted Requests -->
        <div class="mb-5">
            <h4 class="mb-4" style="color: var(--primary-color); font-weight: 700;">
                <i class="fas fa-hammer me-2" style="color: var(--accent-blue);"></i> Ongoing / Accepted
            </h4>

            @if (count($active_requests) > 0)
                <div class="requests-grid">
                    @foreach ($active_requests as $req)
                        <div class="request-card-minimal accepted" id="request-{{ $req->id }}">
                            <div class="header">
                                <h5 class="title">Service with {{ $req->provider_name }}</h5>
                                <span class="badge-minimal accepted">Accepted</span>
                            </div>
                            <div class="meta-grid">
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>{{ \Carbon\Carbon::parse($req->service_date)->format('F j, Y, g:i a') }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-phone"></i>
                                    <span>{{ $req->provider_contact }}</span>
                                </div>
                            </div>
                            @if (!empty($req->notes))
                                <div class="notes">
                                    <strong>Your Note:</strong><br>
                                    {!! nl2br(e($req->notes)) !!}
                                </div>
                            @endif
                            <div class="actions">
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4"
                                    onclick="updateStatus({{ $req->id }}, 'Cancelled')">
                                    Not Completed
                                </button>
                                <button type="button" class="btn btn-accent btn-sm rounded-pill px-4"
                                    onclick="updateStatus({{ $req->id }}, 'Completed')">
                                    Mark as Done
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 rounded-3" style="background: #f8f9fa; border: 1px dashed #dee2e6;">
                    <p class="text-muted mb-0">No active requests at the moment.</p>
                </div>
            @endif
        </div>

        <!-- Pending Requests -->
        <div class="mb-5">
            <h4 class="mb-4" style="color: var(--primary-color); font-weight: 700;">
                <i class="fas fa-clock me-2" style="color: #ffc107;"></i> Pending Requests
            </h4>
            @if (count($pending_requests) > 0)
                <div class="requests-grid">
                    @foreach ($pending_requests as $req)
                        <div class="request-card-minimal pending" id="request-{{ $req->id }}">
                            <div class="header">
                                <h5 class="title">Service with {{ $req->provider_name }}</h5>
                                <span class="badge-minimal pending">Waiting for response</span>
                            </div>
                            <div class="meta-grid">
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>{{ \Carbon\Carbon::parse($req->service_date)->format('F j, Y, g:i a') }}</span>
                                </div>
                            </div>
                            <div class="actions">
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-4"
                                    onclick="updateStatus({{ $req->id }}, 'Cancelled')">
                                    Cancel Request
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-muted">No pending requests.</p>
                </div>
            @endif
        </div>

        <!-- History -->
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 style="color: #6c757d; font-weight: 700; margin: 0;">
                    <i class="fas fa-history me-2"></i> Past Services
                </h4>
                @if (count($history_requests) > 0)
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAllHistory">
                            <label class="form-check-label text-muted" for="selectAllHistory">Select All</label>
                        </div>
                        <button id="bulkDeleteBtn" class="btn btn-danger btn-sm rounded-pill px-3" style="display: none;"
                            onclick="bulkDelete()">
                            <i class="fas fa-trash-alt me-1"></i> Delete
                        </button>
                    </div>
                @endif
            </div>

            @if (count($history_requests) > 0)
                <div class="requests-grid">
                    @foreach ($history_requests as $h)
                        <div class="request-card-minimal history-item {{ strtolower($h->status) == 'completed' ? 'completed' : 'cancelled' }}">
                            <div class="header">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input history-checkbox" type="checkbox"
                                            value="{{ $h->id }}" onchange="toggleDeleteBtn()">
                                    </div>
                                    <h5 class="title text-muted mb-0">Service with {{ $h->provider_name }}</h5>
                                </div>
                                <span
                                    class="badge-minimal {{ strtolower($h->status) == 'completed' ? 'completed' : 'cancelled' }}">
                                    {{ $h->status }}
                                </span>
                            </div>
                            <div class="meta-grid ps-4 ms-2">
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>{{ \Carbon\Carbon::parse($h->service_date)->format('M j, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-muted">No history yet.</p>
                </div>
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
            // Bulk Delete Logic
            const selectAllCheckbox = document.getElementById('selectAllHistory');
            const historyCheckboxes = document.querySelectorAll('.history-checkbox');
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    historyCheckboxes.forEach(cb => cb.checked = isChecked);
                    toggleDeleteBtn();
                });
            }

            function toggleDeleteBtn() {
                const checkedCount = document.querySelectorAll('.history-checkbox:checked').length;
                bulkDeleteBtn.style.display = checkedCount > 0 ? 'block' : 'none';
                
                // Update select all state
                if (checkedCount === historyCheckboxes.length && historyCheckboxes.length > 0) {
                   selectAllCheckbox.checked = true;
                   selectAllCheckbox.indeterminate = false;
                } else if (checkedCount > 0) {
                   selectAllCheckbox.checked = false;
                   selectAllCheckbox.indeterminate = true;
                } else {
                   selectAllCheckbox.checked = false;
                   selectAllCheckbox.indeterminate = false;
                }
            }

            function bulkDelete() {
                const selectedIds = Array.from(document.querySelectorAll('.history-checkbox:checked'))
                    .map(cb => cb.value);

                if (selectedIds.length === 0) return;

                if (!confirm(`Are you sure you want to delete ${selectedIds.length} item(s)? This cannot be undone.`)) return;

                fetch('{{ route('requests.bulk_delete') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            ids: selectedIds
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
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