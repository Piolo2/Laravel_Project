@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                            placeholder="Search by username or email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="seeker" {{ request('role') == 'seeker' ? 'selected' : '' }}>Service Seeker</option>
                        <option value="resident" {{ request('role') == 'resident' ? 'selected' : '' }}>Service Provider
                        </option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.users.index', ['role' => 'pending_verification']) }}"
                        class="btn btn-outline-warning w-100 position-relative">
                        Pending Verification
                        @if($pendingCount > 0)
                            <span
                                class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">New alerts</span>
                            </span>
                        @endif
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>User Details</th>
                        <th>Role</th>
                        <th>Joined Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="ps-4 text-muted">#{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3 text-secondary fw-bold border"
                                        style="width: 40px; height: 40px;">
                                        {{ substr($user->username, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium text-dark">{{ $user->username }}</div>
                                        <div class="small text-muted">{{ $user->email }}</div>
                                        @if($user->providerVerification && $user->providerVerification->status === 'pending')
                                            <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">
                                                <i class="bi bi-exclamation-circle-fill"></i> Verification Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $roleColor = match ($user->role) {
                                        'admin' => 'danger',
                                        'resident' => 'success',
                                        default => 'info'
                                    };
                                    $roleLabel = match ($user->role) {
                                        'resident' => 'Service Provider',
                                        'seeker' => 'Service Seeker',
                                        default => ucfirst($user->role)
                                    };
                                @endphp
                                <span
                                    class="badge bg-{{ $roleColor }} bg-opacity-10 text-{{ $roleColor }} px-3 py-2 rounded-pill">
                                    {{ $roleLabel }}
                                </span>
                            </td>
                            <td class="text-muted">
                                <i class="bi bi-calendar3 me-2"></i>{{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                        class="btn btn-sm btn-outline-secondary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editUserModal{{ $user->id }}" title="Edit Role">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" {{ $user->id == auth()->id() ? 'disabled' : '' }} title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title">Edit User Role</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3 text-secondary fw-bold"
                                                    style="width: 48px; height: 48px;">
                                                    {{ substr($user->username, 0, 1) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $user->username }}</h6>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="role-{{ $user->id }}"
                                                    class="form-label text-muted small text-uppercase fw-bold">Assign
                                                    Role</label>
                                                <select name="role" id="role-{{ $user->id }}"
                                                    class="form-select form-select-lg">
                                                    <option value="seeker" {{ $user->role == 'seeker' ? 'selected' : '' }}>Service
                                                        Seeker
                                                    </option>
                                                    <option value="resident" {{ $user->role == 'resident' ? 'selected' : '' }}>
                                                        Service Provider</option>
                                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>
                                                        Administrator</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection