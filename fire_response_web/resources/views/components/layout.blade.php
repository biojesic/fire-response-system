<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Default Title')</title>

    <!-- In your HTML head -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    {{-- Alpine JS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- CSS/JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Add some custom JS to handle the confirmation dialog -->
    <script>
        function confirmLogout(event) {
            event.preventDefault(); // Prevent form submission
            if (confirm("Are you sure you want to log out?")) {
                event.target.submit(); // If confirmed, submit the form
            }
        }
    </script>

</head>

<body class="bg-slate-200 text-slate-950 min-h-screen flex flex-col">
    <!-- Sticky Navbar -->
    <header class="bg-red-800 text-white shadow-lg sticky top-0 z-100">
        <nav>
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/bfp-logo.png') }}" alt="Logo" class="h-10 w-10">
                <h1>Bureau of Fire Protection</h1>
            </div>
            <div class="flex items-center gap-4">
                @guest
                    <a href="{{ route('home') }}" class="custom-button">Home</a>
                    <a href="{{ route('barangay.registerView') }}" class="custom-button">Register Barangay</a>
                    <a href="{{ route('login') }}" class="custom-button">Login</a>
                @endguest

                @auth
                    @if (auth()->user()->userRole == 'Barangay')
                        <form action="{{ route('logout') }}" method="POST" class="inline" onsubmit="confirmLogout(event)">
                            @csrf
                            <button type="submit" class="nav-link text-white">Logout</button>
                        </form>
                    @endif
                @endauth
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="pl-50 max-w-screen-xl flex-1">
        {{ $slot }}
    </main>

    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
</body>

</html>
