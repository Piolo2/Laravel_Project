@extends('layouts.app')

@section('title', 'My Profile | Uni-Serve')

@push('styles')
    <style>
        /* Add any profile-specific styles here if needed */
    </style>
@endpush

@section('content')
    <div class="container profile-page">
        <div class="profile-header-container">
            <h2 style="margin:0;">My Profile</h2>
            @if (Auth::user()->role === 'resident')
                <span class="badge badge-primary"
                    style="background:#007bff; color:white; padding:5px 10px; border-radius:4px;">Service Provider</span>
            @elseif (Auth::user()->role === 'seeker')
                <span class="badge badge-secondary"
                    style="background:#6c757d; color:white; padding:5px 10px; border-radius:4px;">Service Seeker</span>
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
                            <div
                                style="width: 100%; height: 100%; border-radius: 50%; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; border: 4px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.15);">
                                <span style="color: #888; font-size: 3rem;"><i class="fas fa-user"></i></span>
                            </div>
                        @endif
                    </div>

                    <h3 style="margin-bottom: 5px;">
                        {{ $profile->full_name ?? Auth::user()->username }}
                        @if(\App\Models\ProviderVerification::where('user_id', Auth::id())->where('status', 'approved')->exists())
                            <i class="fas fa-check-circle" style="color: #007bff; font-size: 0.8em;"
                                title="Verified Provider"></i>
                        @endif
                    </h3>
                    <p style="color:#777; margin-top:0;">{{ ucfirst(Auth::user()->role) }}</p>

                    <div class="upload-btn-wrapper">
                        <button type="button" class="btn"
                            style="border: 2px solid var(--accent-blue); color: var(--accent-blue); background: transparent; padding: 8px 20px; border-radius: 20px; font-weight: 600;">
                            <i class="fas fa-camera"></i> Change Photo
                        </button>
                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*"
                            style="position: absolute; left: 0; top: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer;"
                            onchange="document.querySelector('.file-name').textContent = this.files[0].name; document.querySelector('.file-name').style.display='block';">
                    </div>
                    <div class="file-name" style="font-size: 0.85em; color: #555; margin-top: 10px; display:none;"></div>

                    @if(Auth::user()->role !== 'admin')
                        <div class="mt-4 text-center">
                            @php
                                $verification = \App\Models\ProviderVerification::where('user_id', Auth::id())->first();
                            @endphp

                            @if(!$verification)
                                <a href="{{ route('verification.show') }}" class="btn btn-outline-primary btn-sm"
                                    style="border-radius: 20px; font-weight: 600;">
                                    <i class="fas fa-check-circle"></i> Verify My Account
                                </a>
                            @elseif($verification->status === 'pending')
                                <div class="badge badge-warning p-2"><i class="fas fa-clock"></i> Verification Pending</div>
                            @elseif($verification->status === 'approved')
                                <div class="badge badge-success p-2"><i class="fas fa-check-double"></i> Account Verified</div>
                            @elseif($verification->status === 'rejected')
                                <a href="{{ route('verification.show') }}" class="btn btn-outline-danger btn-sm"
                                    style="border-radius: 20px; font-weight: 600;">
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
                            style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
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
                            <div id="map" style="width: 100%; border-radius: 8px;"></div>
                            <p style="font-size: 0.9rem; color: #666; margin-top: 8px;">
                                <i class="fas fa-info-circle"></i> Click on the map to set your exact location for service
                                seekers nearby.
                            </p>
                        </div>

                        <!-- Accomplishments Section -->
                        <div style="margin-top: 35px; border-top: 1px solid #eee; padding-top: 25px;">
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <h3 class="form-section-title" style="margin: 0;">My Accomplishments</h3>
                                @if($accomplishments->count() < 7)
                                    <button type="button" onclick="document.getElementById('uploadModal').style.display='block'"
                                        class="btn-outline-primary"
                                        style="padding: 5px 15px; border-radius: 20px; font-size: 0.9em;">
                                        <i class="fas fa-plus"></i> Add New
                                    </button>
                                @else
                                    <span style="color: #666; font-size: 0.9em; font-style:italic;">Maximum 7 images reached</span>
                                @endif
                            </div>

                            <div style="display: flex; gap: 15px; overflow-x: auto; padding-bottom: 15px;">
                                @forelse($accomplishments as $item)
                                    <div style="position: relative; flex: 0 0 auto; width: 220px;">
                                        <img src="{{ asset($item->image_path) }}" alt="Accomplishment"
                                            style="width: 100%; height: 140px; object-fit: cover; border-radius: 8px;">

                                        <button type="submit" form="delete-form-{{ $item->id }}"
                                            style="position: absolute; top: 5px; right: 5px; background: rgba(220, 53, 69, 0.9); color: white; border: none; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.3s;"
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
                        <button type="submit" class="btn-primary"
                            style="padding: 12px 30px; font-size: 1rem; border-radius: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
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
            style="background-color: #fefefe; margin: 15% auto; padding: 25px; border: 1px solid #888; width: 90%; max-width: 500px; border-radius: 12px; position: relative; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
            <span onclick="document.getElementById('uploadModal').style.display='none'"
                style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <h3 style="color: var(--primary-color); margin-top: 0; margin-bottom: 20px;">Add Accomplishment</h3>
            <form action="{{ route('accomplishments.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label style="font-weight:600;">Upload Image</label>
                    <input type="file" name="image" required class="form-control"
                        style="width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <div class="form-group" style="margin-top: 20px;">
                    <label style="font-weight:600;">Caption (Optional)</label>
                    <input type="text" name="caption" class="form-control" placeholder="Short description..."
                        style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
                <button type="submit" class="btn-primary"
                    style="width: 100%; margin-top: 25px; padding: 12px; border-radius: 8px;">Upload Photo</button>
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