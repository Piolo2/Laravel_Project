@extends('layouts.app')

@section('title', 'Manage Services | Uni-Serve')

@section('content')
    <div class="container my-5">
        <div class="row">
            <!-- Alerts Section -->
            <div class="col-12 mb-3">
                <div id="alert-container">
                    @if (session('msg'))
                        <div class="alert alert-success shadow-sm border-0">{{ session('msg') }}</div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success shadow-sm border-0">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger shadow-sm border-0">{{ session('error') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Section: Form & Instructions -->
        <div class="row mb-4">
            <!-- Left Column: Add Service Form -->
            <div class="col-md-7 mb-4 mb-md-0">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                        <h5 class="mb-0">Add New Service</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('services.add') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted small text-uppercase">Select Skill</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i
                                            class="fas fa-tools text-muted"></i></span>
                                    <select name="skill_id" class="form-control bg-light border-0" required>
                                        <option value="">-- Choose Skill --</option>
                                        @foreach($skills_by_category as $cat => $skills)
                                            <optgroup label="{{ $cat }}">
                                                @foreach($skills as $skill)
                                                    <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-muted small text-uppercase">Description / Rates / Notes
                                    (Optional)</label>
                                <textarea name="description" class="form-control bg-light border-0" rows="3"
                                    placeholder="e.g. 500 per visit, available weekends only"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                <i class="fas fa-plus-circle mr-2"></i> Add Service
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Instructions -->
            <div class="col-md-5">
                <div class="card shadow-sm border-0 h-100 bg-white">
                    <div class="card-body p-4">
                        <h4 class="card-title text-primary mb-3">Manage Offerings</h4>
                        <p class="text-muted">List the services you want to be discovered for. This helps clients find the
                            right professional for their needs.</p>

                        <div class="alert alert-info border-0 bg-light">
                            <h6 class="alert-heading text-dark"><i
                                    class="fas fa-map-marker-alt text-primary mr-2"></i>Location Visibility</h6>
                            <p class="mb-0 small text-muted">Make sure your <strong>Profile Location</strong> is set so
                                users can find you on the map!</p>
                        </div>

                        @if(empty($profile->latitude))
                            <div class="alert alert-danger shadow-sm border-0 mt-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Warning: Your map location is not set.
                                <a href="{{ route('profile') }}" class="font-weight-bold">Set it here</a>.
                            </div>
                        @else
                            <div class="mt-3 text-success small">
                                <i class="fas fa-check-circle mr-1"></i> Location set
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Active Services Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <h4 class="mb-0">My Active Services</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th class="pl-4 border-top-0">Category</th>
                                        <th class="border-top-0">Skill</th>
                                        <th class="border-top-0">Description</th>
                                        <th class="border-top-0">Status</th>
                                        <th class="border-top-0 text-end pr-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($my_skills as $item)
                                        <tr>
                                            <td class="pl-4 align-middle">{{ $item->category_name }}</td>
                                            <td class="align-middle"><span
                                                    class="font-weight-bold text-dark">{{ $item->skill_name }}</span></td>
                                            <td class="align-middle text-muted">{{Str::limit($item->description, 50)}}</td>
                                            <td class="align-middle">
                                                <span id="status-badge-{{ $item->pivot_id }}"
                                                    class="badge rounded-pill bg-{{ $item->availability_status == 'Available' ? 'success' : 'danger' }} px-3 py-2">
                                                    {{ $item->availability_status ?: 'Unavailable' }}
                                                </span>
                                            </td>
                                            <td class="text-end pr-4 align-middle">
                                                <div class="actions" id="actions-{{ $item->pivot_id }}">
                                                    @if($item->availability_status == 'Available')
                                                        <a href="javascript:void(0)"
                                                            onclick="toggleStatus({{ $item->pivot_id }}, 'Unavailable')"
                                                            class="btn btn-sm btn-outline-warning border-0 font-weight-bold">Mark
                                                            Unavailable</a>
                                                    @else
                                                        <a href="javascript:void(0)"
                                                            onclick="toggleStatus({{ $item->pivot_id }}, 'Available')"
                                                            class="btn btn-sm btn-outline-success border-0 font-weight-bold">Mark
                                                            Available</a>
                                                    @endif

                                                    <button type="button" onclick="deleteService({{ $item->pivot_id }})"
                                                        class="btn btn-sm btn-outline-danger border-0"><i
                                                            class="fas fa-trash-alt"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center p-5 text-muted">
                                                <i class="fas fa-box-open fa-3x mb-3 text-light-gray d-block"></i>
                                                No services added yet. Start by adding one above.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleStatus(id, newStatus) {
                console.log('Toggling status:', id, newStatus);
                const badge = document.getElementById(`status-badge-${id}`);
                const actionContainer = document.getElementById(`actions-${id}`);

                // Optimistic UI update (optional, but good for perceived speed)
                // However, we'll wait for server confirmation to be safe, or show a spinner.

                fetch(`/services/toggle/${id}/${newStatus}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response:', data);
                    if (data.success) {
                        // Update Badge
                        if (badge) {
                            badge.textContent = newStatus;
                            badge.classList.remove('bg-success', 'bg-danger');
                            badge.classList.add(newStatus === 'Available' ? 'bg-success' : 'bg-danger');
                        }

                        // Update Action Button
                        if (actionContainer) {
                            // Clear existing buttons (except delete, maybe? The original code had them together)
                            // We need to preserve the delete button. 
                            // Easier to just find the toggle button specifically if we put a class on it, 
                            // OR just replace the specific toggle button HTML.
                            
                            // Let's reconstruct the toggle button HTML based on the new status
                            let toggleBtnHtml = '';
                            if (newStatus === 'Available') {
                                // Now is available, show "Mark Unavailable"
                                toggleBtnHtml = `<a href="javascript:void(0)" 
                                    onclick="toggleStatus(${id}, 'Unavailable')" 
                                    class="btn btn-sm btn-outline-warning border-0 font-weight-bold">Mark Unavailable</a>`;
                            } else {
                                // Now is unavailable, show "Mark Available"
                                toggleBtnHtml = `<a href="javascript:void(0)" 
                                    onclick="toggleStatus(${id}, 'Available')" 
                                    class="btn btn-sm btn-outline-success border-0 font-weight-bold">Mark Available</a>`;
                            }

                            // We need to keep the delete button. The original structure was:
                            // <div id="actions-...">
                            //    <a>Toggle</a>
                            //    <button>Delete</button>
                            // </div>
                            
                            // Let's find the existing toggle button (anchor tag) and replace it.
                            const existingToggle = actionContainer.querySelector('a');
                            if (existingToggle) {
                                existingToggle.outerHTML = toggleBtnHtml;
                            } else {
                                // Fallback if somehow missing, prepend.
                                actionContainer.insertAdjacentHTML('afterbegin', toggleBtnHtml);
                            }
                        }
                        
                        // Optional: Show a toast? The original had an alert-container, but we are preventing reload.
                        // We could inject a success alert at the top if we wanted.
                    } else {
                        alert('Error updating status: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating status. Please check console for details.');
                });
            }

            function deleteService(id) {
                if (!confirm('Remove this service?')) return;

                fetch(`/services/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload(); // Reload to refresh list or we can remove row
                        }
                    });
            }
        </script>
    @endpush
@endsection