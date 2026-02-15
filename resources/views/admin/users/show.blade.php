@extends('layouts.admin')

@section('title', 'User Profile: ' . $user->username)

@section('content')
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Back
        to Users</a>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="rounded-circle bg-primary text-white mx-auto d-flex align-items-center justify-content-center mb-3"
                        style="width: 100px; height: 100px; font-size: 2rem;">
                        {{ substr($user->username, 0, 1) }}
                    </div>
                    <h4>{{ $user->username }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    <span class="badge bg-info fs-6">{{ ucfirst($user->role) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Profile Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">Full Name</div>
                            <div class="col-sm-9">{{ $user->profile->full_name ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">Contact Number</div>
                            <div class="col-sm-9">{{ $user->profile->contact_number ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">Address</div>
                            <div class="col-sm-9">{{ $user->profile->address ?? 'N/A' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3 fw-bold">Joined</div>
                            <div class="col-sm-9">{{ $user->created_at->format('F d, Y') }}</div>
                        </div>
                    </div>
                </div>

                @if($user->role == 'resident' || $user->role == 'provider')
                    <div class="card mt-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Skills / Services</h5>
                        </div>
                        <div class="card-body">
                            @if($user->userSkills && $user->userSkills->count() > 0)
                                <ul class="list-group">
                                    @foreach($user->userSkills as $userSkill)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $userSkill->skill->name ?? 'Unknown Skill' }}
                                            <span
                                                class="badge bg-secondary">{{ $userSkill->skill->category->name ?? 'Uncategorized' }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">No skills listed.</p>
                            @endif
                        </div>
                    </div>
                @endif

                @if($user->providerVerification)
                    <div
                        class="card mt-4 border-{{ $user->providerVerification->status === 'pending' ? 'warning' : 'secondary' }}">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Provider Verification Request</h5>
                            <span
                                class="badge bg-{{ $user->providerVerification->status === 'pending' ? 'warning' : ($user->providerVerification->status === 'approved' ? 'success' : 'danger') }}">
                                {{ ucfirst($user->providerVerification->status) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Personal Info</h6>
                                    <p><strong>Name:</strong> {{ $user->providerVerification->first_name }}
                                        {{ $user->providerVerification->last_name }}
                                    </p>
                                    <p><strong>Address:</strong> {{ $user->providerVerification->address }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Professional Info</h6>
                                    <p><strong>Service Type:</strong> {{ $user->providerVerification->service_type ?? 'N/A' }}
                                    </p>
                                    <p><strong>Work Status:</strong> {{ $user->providerVerification->work_status ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <h6 class="text-muted mt-3">Documents</h6>
                            <div class="d-flex gap-3 flex-wrap">
                                @if($user->providerVerification->id_front_file)
                                    <div>
                                        <small class="d-block mb-1">ID Front</small>
                                        <a href="{{ Storage::url($user->providerVerification->id_front_file) }}" target="_blank">
                                            <img src="{{ Storage::url($user->providerVerification->id_front_file) }}" alt="ID Front"
                                                class="img-thumbnail" style="height: 100px;">
                                        </a>
                                    </div>
                                @endif
                                @if($user->providerVerification->id_back_file)
                                    <div>
                                        <small class="d-block mb-1">ID Back</small>
                                        <a href="{{ Storage::url($user->providerVerification->id_back_file) }}" target="_blank">
                                            <img src="{{ Storage::url($user->providerVerification->id_back_file) }}" alt="ID Back"
                                                class="img-thumbnail" style="height: 100px;">
                                        </a>
                                    </div>
                                @endif
                                @if($user->providerVerification->compliance_certificate_file)
                                    <div>
                                        <small class="d-block mb-1">Certificate</small>
                                        <a href="{{ Storage::url($user->providerVerification->compliance_certificate_file) }}"
                                            target="_blank">
                                            <img src="{{ Storage::url($user->providerVerification->compliance_certificate_file) }}"
                                                alt="Cert" class="img-thumbnail" style="height: 100px;">
                                        </a>
                                    </div>
                                @endif
                            </div>

                            @if($user->providerVerification->status === 'pending')
                                <div class="mt-4 pt-3 border-top d-flex gap-2">
                                    <form action="{{ route('admin.users.verify', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Approve this provider?');">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> Verify Account
                                        </button>
                                    </form>
                                    <button class="btn btn-danger"
                                        onclick="document.getElementById('rejectForm').style.display = 'block'">
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>
                                </div>

                                <div id="rejectForm" style="display: none;" class="mt-3">
                                    <form action="{{ route('admin.users.reject', $user->id) }}" method="POST">
                                        @csrf
                                        <div class="mb-2">
                                            <label for="rejection_reason">Rejection Reason</label>
                                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="2"
                                                required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-danger">Confirm Rejection</button>
                                        <button type="button" class="btn btn-sm btn-secondary"
                                            onclick="document.getElementById('rejectForm').style.display = 'none'">Cancel</button>
                                    </form>
                                </div>
                            @elseif($user->providerVerification->status === 'rejected')
                                <div class="alert alert-danger mt-3">
                                    <strong>Rejection Reason:</strong> {{ $user->providerVerification->rejection_reason }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
@endsection

