@extends('layouts.app')

@section('title', 'Find Nearby Services | Uni-Serve')

@section('content')
    <div class="container">
        <div class="seeker-header">
            <h2>Find Nearby Services in Unisan</h2>
            <p>Explore skilled professionals in your area using the map.</p>
        </div>

        <!-- Filters Bar (Horizontal) -->
        <div class="card"
            style="margin-bottom: 30px; padding: 20px; border: 1px solid #e0e0e0; background: #fff; border-radius: 8px;">
            <div class="search-filters">
                <div class="filter-group-keyword">
                    <label for="keyword" style="display: block; margin-bottom: 5px; font-weight: 600;">Keyword</label>
                    <input type="text" id="keyword" placeholder="Search service or name..."
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                <div class="filter-group-category">
                    <label for="category" style="display: block; margin-bottom: 5px; font-weight: 600;">Category</label>
                    <select id="category" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                        <option value="">All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group-distance">
                    <label for="distance" style="display: block; margin-bottom: 5px; font-weight: 600;">Max Dist (km)</label>
                    <input type="number" id="distance" value="10"
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                </div>
                <div class="filter-submit">
                    <button id="filterBtn"
                        style="padding: 10px 25px; background-color: var(--primary-color, #0d2c52); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600; height: 100%; width: 100%;">Filter</button>
                </div>
            </div>
        </div>

        <!-- Map Section (Original Map Container Style) -->
        <div class="map-container-wrapper">
            <div id="map" style="width: 100%; height: 100%; border-radius: 10px; z-index: 1;"></div>
        </div>

        <!-- Results Section -->
        <div class="section-title"
            style="text-align: left; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3>Available Services</h3>
                <p>Showing <span id="resultCount">0</span> providers near you</p>
            </div>
        </div>

        <!-- Results Grid -->
        <div id="resultsGrid" class="service-grid">
            <!-- Javascript will populate this -->
            <p>Loading...</p>
        </div>
    </div>

    <!-- Hidden inputs for User Location -->
    <input type="hidden" id="myLat" value="{{ $userLat ?? '' }}">
    <input type="hidden" id="myLng" value="{{ $userLng ?? '' }}">
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/search-map.js') }}"></script>
@endpush

