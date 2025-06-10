<x-layout>
    @section('title', 'Fire Emergency - Login')

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            position: relative;
            background-image: url('{{ asset('images/bfp-bg1.webp') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        /* Overlay for semi-transparent effect */
        body::before {
            content: '';
            /* Empty content for the pseudo-element */
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(246, 245, 245, 0.7);
            /* Semi-transparent black overlay */
            z-index: -1;
            /* Ensure the overlay is behind the content */
        }
    </style>

    <div class="mx-auto max-w-md card mt-20">
        <form action="{{ route('login.submit') }}" method="post">
            @csrf
            {{-- EMAIL --}}
            <div class="mb-4">
                <label for="email">Email</label>
                <input type="text" name="email" value="{{ old('email') }}"
                    class="input @error('email') ring-red-500 @enderror">
            </div>

            {{-- PASSWORD --}}
            <div class="mb-4">
                <label for="password">Password</label>
                <input type="password" name="password" class="input">

            </div>

            {{-- REMEMBER ME --}}
            <div class="mb-8">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember me</label>
            </div>
            @error('failed')
                <p class="text-red-500">{{ $message }}</p>
            @enderror

            <div class="mb-4">
                <button class="btn">LOGIN</button>
            </div>
            <div class="mb-4">
                <a href="">Forgot password?</a>
            </div>
        </form>
    </div>
</x-layout>
