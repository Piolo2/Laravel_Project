@extends('layouts.app')

@section('title', 'My Profile | Uni-Serve')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
@endpush

@section('content')
    <div class="container profile-page">
        <div class="profile-header-container">
            <h2 style="margin:0;">My Profile</h2>
            @if (Auth::user()->role === 'resident')
                <span class="badge badge-primary profile-badge badge-provider">Service Provider</span>
            @elseif (Auth::user()->role === 'seeker')
                <span class="badge badge-secondary profile-badge badge-seeker">Service Seeker</span>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul style="margin-bottom: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="profile-grid">
                <!-- 1. Sidebar: Identity -->
                <div class="profile-sidebar">
                    <div class="profile-img-container">
                        @if (!empty($profile->profile_picture))
                            <img src="{{ asset($profile->profile_picture) }}" alt="Profile Picture" class="profile-img-main">
                        @else
                            <div class="profile-img-placeholder">
                                <span class="profile-icon"><i class="fas fa-user"></i></span>
                            </div>
                        @endif
                    </div>

                    <h3 style="margin-bottom: 5px;">
                        {{ $profile->full_name ?? Auth::user()->username }}
                        @if($verification && $verification->status === 'approved')
                            <i class="fas fa-check-circle verified-icon" title="Verified Provider"></i>
                        @endif
                    </h3>
                    <p class="role-text">{{ ucfirst(Auth::user()->role) }}</p>

                    <div class="upload-btn-wrapper">
                        <button type="button" class="btn change-photo-btn">
                            <i class="fas fa-camera"></i> Change Photo
                        </button>
                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="file-input"
                            onchange="document.querySelector('.file-name').textContent = this.files[0].name; document.querySelector('.file-name').style.display='block';">
                    </div>
                    <div class="file-name file-name-display"></div>

                    @if(Auth::user()->role !== 'admin')
                        <div class="mt-4 text-center">
                            {{-- Verification status logic handled in controller --}}

                            @if(!$verification)
                                <a href="{{ route('verification.show') }}" class="btn btn-outline-primary btn-sm btn-verify">
                                    <i class="fas fa-check-circle"></i> Verify My Account
                                </a>
                            @elseif($verification->status === 'pending')
                                <div class="badge badge-warning p-2"><i class="fas fa-clock"></i> Verification Pending</div>
                            @elseif($verification->status === 'approved')
                                <div class="badge badge-success p-2"><i class="fas fa-check-double"></i> Account Verified</div>
                            @elseif($verification->status === 'rejected')
                                <a href="{{ route('verification.show') }}" class="btn btn-outline-danger btn-sm btn-verify">
                                    <i class="fas fa-exclamation-circle"></i> Verification Rejected (Retry)
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- 2. Main Content: Details -->
                <div class="profile-content">
                    <h3 class="form-section-title">Personal Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" value="{{ old('full_name', $profile->full_name ?? '') }}"
                                required>
                        </div>

                        <div class="form-group">
                            <label>Contact Number
                                {!! (Auth::user()->role === 'resident') ? '<small class="text-muted">(Public)</small>' : '' !!}</label>
                            <input type="text" name="contact_number"
                                value="{{ old('contact_number', $profile->contact_number ?? '') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Bio</label>
                        <textarea name="bio" rows="4"
                            style="resize: vertical;">{{ old('bio', $profile->bio ?? '') }}</textarea>
                    </div>

                    <h3 class="form-section-title">Location Details</h3>
                    <div class="form-group">
                        <label>Barangay (Unisan, Quezon) <span style="color:red;">*</span></label>
                        <select name="address" id="barangaySelect" required
                            style="width: 100%; padding: 10px; border: 1px solid #ccc;">
                            <option value="">-- Select Barangay --</option>
                            @foreach($barangays as $brgy)
                                @php
                                    // Check if the current profile address starts with this barangay
                                    // (since we append ", Unisan, Quezon" on save)
                                    $isSelected = old('address') == $brgy || (isset($profile->address) && strpos($profile->address, $brgy) === 0);
                                @endphp
                                <option value="{{ $brgy }}" {{ $isSelected ? 'selected' : '' }}>
                                    {{ $brgy }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Hidden Geo Inputs -->
                    <input type="hidden" name="latitude" id="latitude"
                        value="{{ old('latitude', $profile->latitude ?? '') }}">
                    <input type="hidden" name="longitude" id="longitude"
                        value="{{ old('longitude', $profile->longitude ?? '') }}">

                    @if (Auth::user()->role !== 'seeker')
                        <div style="margin-top: 25px;">
                            <label style="margin-bottom: 10px; display: block;">Pin Location on Map</label>
                            <div id="map" class="map-container"></div>
                            <p class="map-info">
                                <i class="fas fa-info-circle"></i> Click on the map to set your exact location for service
                                seekers nearby.
                            </p>
                        </div>

                        <!-- Accomplishments Section -->
                        <div style="margin-top: 35px; border-top: 1px solid #eee; padding-top: 25px;">
                            <div class="accomplishments-header">
                                <h3 class="form-section-title" style="margin: 0;">My Accomplishments</h3>
                                @if($accomplishments->count() < 7)
                                    <button type="button" onclick="document.getElementById('uploadModal').style.display='block'"
                                        class="btn-outline-primary" style="padding: 5px 15px; font-size: 0.9em;">
                                        <i class="fas fa-plus"></i> Add New
                                    </button>
                                @else
                                    <span style="color: #666; font-size: 0.9em; font-style:italic;">Maximum 7 images reached</span>
                                @endif
                            </div>

                            <div class="accomplishments-scroll">
                                @forelse($accomplishments as $item)
                                    <div class="accomplishment-item">
                                        <img src="{{ asset($item->image_path) }}" alt="Accomplishment" class="accomplishment-img">

                                        <button type="submit" form="delete-form-{{ $item->id }}" class="delete-btn"
                                            onclick="return confirm('Remove this image?')" title="Delete">
                                            <i class="fas fa-trash" style="font-size: 0.8em;"></i>
                                        </button>

                                        @if($item->caption)
                                            <p style="font-size: 0.85em; margin-top: 5px; color: #555;">{{ $item->caption }}</p>
                                        @endif
                                    </div>
                                @empty
                                    <p style="color: #888; font-style: italic; width: 100%; text-align: center;">No accomplishments
                                        added yet.</p>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <div style="margin-top: 30px; text-align: right;">
                        <button type="submit" class="btn-primary save-btn">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal" class="modal"
        style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content"
            style="background-color: #fefefe; margin: 15% auto; padding: 25px; border: 1px solid #888; width: 90%; max-width: 500px; position: relative; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
            <span onclick="document.getElementById('uploadModal').style.display='none'"
                style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <h3 style="color: var(--primary-color); margin-top: 0; margin-bottom: 20px;">Add Accomplishment</h3>
            <form action="{{ route('accomplishments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label style="font-weight:600;">Upload Image</label>
                    <input type="file" name="image" required class="form-control"
                        style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd;">
                </div>
                <div class="form-group" style="margin-top: 20px;">
                    <label style="font-weight:600;">Caption (Optional)</label>
                    <input type="text" name="caption" class="form-control" placeholder="Short description..."
                        style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd;">
                </div>
                style="width: 100%; margin-top: 25px; padding: 12px;">Upload Photo</button>
            </form>
        </div>
    </div>

    @if (isset($accomplishments) && $accomplishments->count() > 0)
        @foreach($accomplishments as $item)
            <form id="delete-form-{{ $item->id }}" action="{{ route('accomplishments.destroy', $item->id) }}" method="POST"
                style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    @endif

    <script>
        // Close modal when clicking outside
        window.onclick = function (event) {
            var modal = document.getElementById('uploadModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
@endsection

@push('scripts')
    @if (Auth::user()->role !== 'seeker')
        <script src="{{ asset('assets/js/profile-map.js') }}"></script>
    @endif
@endpush