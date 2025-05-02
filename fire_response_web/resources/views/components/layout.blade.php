<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Default Title')</title>

    {{-- Alpine JS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- CSS/JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-slate-200 text-slate-950">
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
                    <a href="{{ route('login') }}" class="custom-button">Login</a>
                @endguest

                {{-- @auth
                    <a href="{{ url('firefighters/register') }}" class="nav-link">Register</a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="nav-link text-white">Logout</button>
                    </form>
                    <div class="relative grid place-items-center" x-data="{ open: false }">
                        <button type="button" class="round-btn" @click="open = ! open">
                            <img src="https://picsum.photos/200" alt="Avatar">
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                            class="bg-white text-black shadow-lg absolute top-10 right-0 rounded-lg overflow-hidden font-light">
                            <p class="px-4 py-2 text-sm text-black"> {{ auth()->user()->userFirstName }} </p>
                            <a href="{{ route('admin.dashboard') }}"
                                class="block hover:bg-slate-100 px-4 py-2 text-sm text-black mt-2">Dashboard</a>
                            <a href="{{ route('logout') }}"
                                class="block hover:bg-slate-100 px-4 py-2 text-sm text-black">Logout</a>
                        </div>
                    </div>
                @endauth --}}
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="pl-50 max-w-screen-xl flex-1">
        {{ $slot }}
    </main>

    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous">
    </script> --}}
    {{-- Google maps --}}
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    </script>
</body>

</html>
