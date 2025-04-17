<x-layout>
    <h1 class="title">Login</h1>

    <div class="mx-auto max-w-screen-sm card">
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
        </form>
    </div>
</x-layout>
