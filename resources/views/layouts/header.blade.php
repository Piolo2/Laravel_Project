<!-- resources/views/layouts/header.blade.php -->
<nav class="navbar">
    <a href="{{ url('/') }}" class="brand">
        <i class="fas fa-hand-holding-heart" style="color:#007bff;"></i> UNI-SERVE
    </a>
    <div class="nav-links">
        @php
            $home_url = url('/');
            if (Auth::check()) {
                $role = Auth::user()->role;
                if ($role == 'resident')
                    $home_url = route('service_provider');
                if ($role == 'seeker')
                    $home_url = route('service_seeker');
                if ($role == 'admin')
                    $home_url = route('admin.dashboard');
            }
        @endphp
        <a href="{{ $home_url }}">Home</a>

        @auth
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            @elseif (Auth::user()->role === 'resident')
                <a href="{{ route('profile') }}">Profile</a>
                <a href="{{ route('services.index') }}">My Skills</a>
                <a href="{{ route('requests') }}">Requests</a>
            @elseif (Auth::user()->role === 'seeker')
                <a href="{{ route('profile') }}">Profile</a>
                <a href="{{ route('my_requests') }}">My Requests</a>
                <a href="{{ route('search') }}">Find Services</a>
            @endif
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit"
                    style="background: none; border: none; color: inherit; cursor: pointer; font: inherit;">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" style="color: var(--accent-blue);">Login</a>
            <a href="{{ route('register') }}"
                style="border: 2px solid var(--primary-color); padding: 5px 15px; margin-left:15px;">Register</a>
        @endauth
    </div>
    <div class="hamburger-menu">
        <i class="fas fa-bars"></i>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hamburger = document.querySelector('.hamburger-menu');
        const navLinks = document.querySelector('.nav-links');

        if (hamburger && navLinks) {
            hamburger.addEventListener('click', function (e) {
                e.preventDefault(); // Prevent default link behavior if any
                navLinks.classList.toggle('active');

                // Icon Toggle Logic
                const icon = hamburger.querySelector('i');
                if (icon) {
                    if (navLinks.classList.contains('active')) {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                        navLinks.style.display = 'flex'; // Force display
                    } else {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                        navLinks.style.display = ''; // Reset display
                    }
                }
            });
        }
    });
</script>


