@extends('layouts.app')

@section('title', 'Uni-Serve | Unisan, Quezon')

@section('content')
    <!-- Hero Section -->
    <header class="hero">
        <div class="hero-content">
            <h1>Connect with Local Service Providers in Unisan, Quezon</h1>
            <p>Discover skilled workers in your community or showcase your skills to neighbors. This Platform uses
                geo-tagging to match Service Providers with Service Seekers in need.</p>

            @auth
                <div style="margin-top: 20px;">
                    <a href="{{ route('search') }}" class="btn btn-accent">Find a Service Now</a>
                </div>
            @else
                <div style="margin-top: 20px;">
                    <a href="{{ route('login') }}" class="btn btn-accent" style="margin-right: 15px;">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-white">Register</a>
                </div>
            @endauth
        </div>
    </header>

    <!-- Split feature section -->
    <section class="split-section" id="features">
        <div class="split-image"
            style="background: url('{{ asset('public/images/worker-placeholder.jpg') }}') center/cover;">
            <!-- Placeholder for worker image. In real app, would be an <img> tag or background-image -->
            <div
                style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#eee;">
                <i class="fas fa-hard-hat" style="font-size: 5rem; color: #ccc;"></i>
            </div>
        </div>
        <div class="split-content">
            <h2>For Service Providers</h2>
            <p>Log in to manage your services, track tasks, and focus on what you do best.</p>
            <p style="margin-top: 20px; font-style: italic;">"Showcase your skills and services to neighbors."</p>

            <div style="margin-top: 30px; display:flex; gap:10px;">
                <span style="width:10px; height:10px; background:white; border-radius:50%;"></span>
                <span style="width:10px; height:10px; background:rgba(255,255,255,0.5); border-radius:50%;"></span>
                <span style="width:10px; height:10px; background:rgba(255,255,255,0.5); border-radius:50%;"></span>
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section class="container" id="how" style="text-align: center; padding: 4rem 0;">
        <h2 style="color: var(--primary-color); margin-bottom: 3rem;">How It Works</h2>
        <div style="display: flex; gap: 20px;">
            <div class="card" style="flex:1;">
                <i class="fas fa-user-plus" style="font-size: 2rem; color: #007bff; margin-bottom:15px;"></i>
                <h3>1. Register</h3>
                <p>Create an account as a Service Provider or Seeker.</p>
            </div>
            <div class="card" style="flex:1;">
                <i class="fas fa-map-marker-alt" style="font-size: 2rem; color: #007bff; margin-bottom:15px;"></i>
                <h3>2. Pin Location</h3>
                <p>Providers set their location on the map for easy discovery.</p>
            </div>
            <div class="card" style="flex:1;">
                <i class="fas fa-search" style="font-size: 2rem; color: #ffc107; margin-bottom:15px;"></i>
                <h3>3. Connect</h3>
                <p>Search via map and contact local providers directly.</p>
            </div>
        </div>
    </section>
@endsection