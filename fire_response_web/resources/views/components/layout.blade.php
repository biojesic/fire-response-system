<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Emergency - Login</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-slate-200 text-slate-950">
    <header class="bg-red-800 text-white shadow-lg">
        <nav>
            <a href="{{ route('home') }}" class="nav-link">Home</a>

            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="nav-link">Login</a>
                <a href="{{ route('register') }}" class="nav-link">Register</a>
            </div>
        </nav>
    </header>

    <main class = "py-8 px-4 mx-auto max-w-screen-lg">
        {{ $slot }}
    </main>
</body>
</html>