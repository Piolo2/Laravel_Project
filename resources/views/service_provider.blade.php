@extends('layouts.app')

@section('title', 'Provider Dashboard | Uni-Serve')

@section('content')
    <!-- Provider Hero -->
    <header class="hero provider-hero-custom"
        style="background: linear-gradient(rgba(13, 44, 82, 0.4), rgba(13, 44, 82, 0.4)), url('{{ asset('images/bghome.png') }}') center/cover; height: 60vh;">
        <div class="hero-content">
            <h1>Welcome Back,
                {{ $profile->full_name ?? 'Partner' }}!
            </h1>
            <p>Your skills help build a better Unisan. Manage your services and keep your availability updated.</p>
            <div style="margin-top: 20px;">
                <a href="{{ route('services.index') }}" class="btn btn-accent">Manage Services</a>
                <a href="{{ route('profile') }}" class="btn btn-outline-white">Edit Profile</a>
            </div>
        </div>
    </header>

    <section class="container" style="padding: 4rem 0;">
        <h2 style="color: var(--primary-color);">Provider Dashboard</h2>

        <div class="provider-dashboard-grid">
            <!-- Status Card -->
            <div class="card" style="flex: 1; border-top: 5px solid var(--accent-green);">
                <i class="fas fa-check-circle" style="font-size: 2rem; color: var(--accent-green); float:right;"></i>
                <h3>Availability</h3>
                <p>You are currently listed as <strong>{{ $profile->availability ?? 'Available' }}</strong>.</p>
                <small>Seekers can find you on the map.</small>
            </div>

            <!-- Location Card -->
            <div class="card" style="flex: 1; border-top: 5px solid var(--accent-blue);">
                <i class="fas fa-map-marker-alt" style="font-size: 2rem; color: var(--accent-blue); float:right;"></i>
                <h3>Location Status</h3>
                @if (!empty($profile->latitude))
                    <p>Location pinned.</p>
                    <a href="{{ route('profile') }}" style="text-decoration:none; color:var(--accent-blue);">Update Pin</a>
                @else
                    <p style="color:red;">Not Set!</p>
                    <a href="{{ route('profile') }}" style="text-decoration:none; font-weight:bold;">Set Location Now</a>
                @endif
            </div>
        </div>

    </section>
@endsection
