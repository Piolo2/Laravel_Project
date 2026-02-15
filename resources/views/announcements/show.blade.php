@extends('layouts.app')

@section('title', $announcement->title . ' - Uni-Serve')

@section('content')
    <div class="container py-5">
        <div class="mb-5">
            <a href="{{ route('home') }}" class="btn btn-dark rounded-0 px-4 back-home-btn text-white"
                style="transition: all 0.3s; background-color: var(--primary-color); border-color: var(--primary-color);">
                <i class="bi bi-arrow-left me-2"></i> BACK TO HOME
            </a>
        </div>

        <div class="card shadow-sm border rounded-0 overflow-hidden">
            <div class="row g-0">
                <div class="col-lg-5 bg-light d-flex align-items-center justify-content-center p-0">
                    @if($announcement->image_path)
                        <img src="{{ asset($announcement->image_path) }}" class="img-fluid w-100 h-100 announcement-img"
                            alt="{{ $announcement->title }}">
                    @else
                        <div class="text-center p-5 text-muted">
                            <i class="bi bi-megaphone fs-1 mb-3"></i>
                            <p>No Image</p>
                        </div>
                    @endif
                </div>
                <div class="col-lg-7">
                    <div class="card-body p-3 p-md-5">
                        <div class="d-flex justify-content-between align-items-start mb-3 mb-md-4">
                            <h1 class="fs-3 fs-md-2 fw-bold text-dark mb-0">{{ $announcement->title }}</h1>
                            @if(\Carbon\Carbon::parse($announcement->deadline)->isPast())
                                <span class="badge bg-secondary rounded-0 px-2 px-md-3 py-2">EXPIRED</span>
                            @else
                                <span class="badge bg-dark rounded-0 px-2 px-md-3 py-2">ACTIVE</span>
                            @endif
                        </div>

                        <div class="d-flex flex-wrap gap-3 gap-md-4 text-secondary text-uppercase small mb-4 mb-md-5"
                            style="letter-spacing: 1px;">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person me-2"></i> {{ $announcement->admin_name }}
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar3 me-2"></i>
                                {{ \Carbon\Carbon::parse($announcement->date_posted)->format('M d, Y') }}
                            </div>
                            <div class="d-flex align-items-center text-dark fw-bold">
                                <i class="bi bi-hourglass-split me-2"></i> Deadline:
                                {{ \Carbon\Carbon::parse($announcement->deadline)->format('M d, Y') }}
                            </div>
                        </div>

                        <hr class="mb-4 text-muted opacity-25">

                        <div class="announcement-content text-dark" style="font-size: 1rem; line-height: 1.7;">
                            {!! nl2br(e($announcement->description)) !!}
                        </div>

                    </div>
                </div>
            </div>
            <style>
                .announcement-img {
                    object-fit: cover;
                    min-height: 250px;
                }

                @media (min-width: 992px) {
                    .announcement-img {
                        min-height: 400px;
                    }

                    .announcement-content {
                        font-size: 1.05rem !important;
                        line-height: 1.8 !important;
                    }

                    h1 {
                        font-size: 2.5rem !important;
                        /* Restore larger size on desktop */
                    }
                }
            </style>
        </div>
    </div>
@endsection


