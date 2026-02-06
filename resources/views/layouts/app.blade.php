<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Uni-Serve | Unisan, Quezon')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}?v={{ time() }}3">
    <link rel="stylesheet" href="{{ asset('assets/css/styles_append.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <link rel="stylesheet" href="{{ asset('css/loader.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/map-pins.css') }}">
    @stack('styles')
</head>

<body data-barba="wrapper">
    <!-- Unique Loader -->
    <div id="loader-wrapper">
        <div class="uni-spinner">
            <div class="center-dot"></div>
        </div>
    </div>

    @include('layouts.header')

    <main data-barba="container" data-barba-namespace="default">
        @yield('content')
        @stack('scripts')
    </main>

    @include('layouts.footer')

    <script>
        // Loader logic handled by app.js now
    </script>
    <script>
        window.addEventListener('load', function () {
            const loader = document.getElementById('loader-wrapper');
            loader.classList.add('loaded');
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        });
    </script>
</body>

</html>