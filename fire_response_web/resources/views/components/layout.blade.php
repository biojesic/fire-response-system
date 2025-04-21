<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Default Title')</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-200 text-slate-950">
    <!-- Sticky Navbar -->
    <header class="bg-red-800 text-white shadow-lg sticky top-0 z-100">
        <nav>
            <h1>BFP</h1>
            <div class="flex items-center gap-4">
                @guest
                    <a href="{{ route('login') }}" class="nav-link">Login</a>
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
    <main class="py-8 px-6 mx-auto max-w-screen-xl flex-1 mt-0 pt-0">
        {{ $slot }}
    </main>
</body>

</html>
