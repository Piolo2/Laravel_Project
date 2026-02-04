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

    <!-- Announcements Section -->
    @if(isset($announcements) && $announcements->count() > 0)
        <!-- Added gap with mt-5 and py-5 -->
        <section id="announcements" class="container mt-5 pt-5">
            <h2 class="text-center mb-5 fw-normal text-uppercase tracking-wider"
                style="color: var(--primary-color); letter-spacing: 2px;">Announcements</h2>

            <div id="announcementCarousel" class="carousel slide carousel-fade shadow-sm border" data-bs-ride="carousel"
                data-bs-interval="3000"> <!-- Increased interval slightly for better read time -->
                <div class="carousel-inner">
                    @foreach($announcements as $index => $announcement)
                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                            <div class="split-section bg-white">
                                <div class="split-image"
                                    style="background: url('{{ $announcement->image_path ? asset($announcement->image_path) : asset('assets/images/hero-bg.jpg') }}') center/cover;">
                                </div>
                                <div class="split-content announcement-content bg-white text-dark p-4 p-md-5"
                                    style="background: white !important; background-image: none !important;">
                                    <h3 class="fw-bold mb-1" style="color: var(--primary-color);">{{ $announcement->title }}</h3>

                                    <div class="d-flex flex-wrap gap-3 text-secondary small mb-3 text-uppercase"
                                        style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                        <span class="d-flex align-items-center">
                                            <i class="bi bi-person-fill me-1"></i> {{ $announcement->admin_name }}
                                        </span>
                                        <span class="d-flex align-items-center">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ \Carbon\Carbon::parse($announcement->date_posted)->format('M d, Y') }}
                                        </span>
                                    </div>

                                    <p class="mb-4 text-secondary" style="line-height: 1.6;">
                                        {{ \Illuminate\Support\Str::limit($announcement->description, 150) }}
                                    </p>

                                    <div class="d-flex align-items-center justify-content-between mt-auto pt-3 border-top">
                                        <div class="text-dark small">
                                            <span class="fw-bold text-uppercase" style="font-size: 0.7rem;">Deadline</span><br>
                                            <span
                                                style="font-family: monospace;">{{ \Carbon\Carbon::parse($announcement->deadline)->format('Y-m-d') }}</span>
                                        </div>
                                        <a href="{{ route('announcement.show', $announcement->id) }}"
                                            class="btn btn-accent rounded-0 px-4 py-2"
                                            style="font-size: 0.85rem; letter-spacing: 0.5px; background-color: var(--primary-color); border-color: var(--primary-color);">
                                            VIEW DETAILS
                                        </a>
                                    </div>
                                </div>
                                <!-- Override pseudo-element for this specific section -->
                                <style>
                                    .announcement-content::before {
                                        content: none !important;
                                        display: none !important;
                                        background: none !important;
                                    }
                                </style>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($announcements->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#announcementCarousel" data-bs-slide="prev"
                        style="width: 5%; opacity: 0.5;">
                        <span class="carousel-control-prev-icon bg-dark rounded-1" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#announcementCarousel" data-bs-slide="next"
                        style="width: 5%; opacity: 0.5;">
                        <span class="carousel-control-next-icon bg-dark rounded-1" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>
        </section>
    @else
        <!-- Fallback or Hidden if no announcements -->
        <!-- Original Content optionally preserved if needed, but user asked to add announcements.
                                                                                         If no announcements, showing nothing is safer than showing incorrect static data. -->
    @endif

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