@extends('layouts.app')

@section('title', 'Service Seeker Dashboard | Uni-Serve')

@section('content')
    <!-- Seeker Hero -->
    <header class="hero"
        style="background: linear-gradient(rgba(13, 44, 82, 0.4), rgba(13, 44, 82, 0.4)), url('{{ asset('images/bghome.png') }}') center/cover; height: 70vh; justify-content: center; text-align: center;">
        <div class="hero-content" style="max-width: 800px;">
            <h1>What service do you need today?</h1>
            <p>Find trusted local workers in Unisan instantly.</p>

            <!-- Large Search Button/Bar -->
            <div
                style="margin-top: 30px; background: white; padding: 10px; border-radius: 50px; display: flex; align-items: center;">
                <i class="fas fa-search" style="color: #666; margin-left: 20px;"></i>
                <input type="text" placeholder="Try 'Plumber' or 'Tutor'..."
                    style="border: none; outline: none; box-shadow: none; font-size: 1.1rem; padding: 10px;">
                <a href="{{ route('search') }}" class="btn btn-accent"
                    style="border-radius: 50px; padding: 10px 40px;">Search</a>
            </div>
        </div>
    </header>

    <section class="container" style="padding: 4rem 0;">
        <h2 class="text-center" style="color: var(--primary-color);">Browse Categories</h2>
        <div class="seeker-categories-container">
            @foreach ($top_cats as $cat)
                <div class="card seeker-category-card">
                    <h3 style="margin: 0;">{{ $cat->name }}</h3>
                    <p style="margin-top: 10px; color: #666;">View Providers</p>
                    <a href="{{ route('search', ['cat' => $cat->name]) }}" aria-label="View {{ $cat->name }} category"
                        style="position:absolute; top:0; left:0; width:100%; height:100%;"></a>
                </div>
            @endforeach
            <div class="card seeker-category-card" style="background: #f8f9fa;">
                <h3 style="margin: 0; color: #007bff;">View All</h3>
                <a href="{{ route('search') }}" aria-label="View all categories" style="position:absolute; top:0; left:0; width:100%; height:100%;"></a>
            </div>
        </div>
    </section>
@endsection
