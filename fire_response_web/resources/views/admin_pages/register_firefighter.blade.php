<x-layout>

    @section('title', 'Fire Emergency - Register')

    <h1 class="title">Create an Account for Firefighter</h1>

    <div class="mx-auto max-w-screen-sm card">
        <form action="{{ route('register-firefighter') }}" method="POST">
            @csrf

            {{-- FIRST NAME --}}
            <div class="mb-4">
                <label for="firstName">First Name</label>
                <input type="text" name="firstName" value="{{ old('firstName') }}"
                    class="input @error('firstName') {{ $message }} ring-red-500 @enderror">
            </div>

            {{-- LAST NAME --}}
            <div class="mb-4">
                <label for="lastName">Last Name</label>
                <input type="text" name="lastName" class="input">
            </div>

            {{-- ADDRESS --}}
            <div class="mb-4">
                <label for="address">Address</label>
                <input type="text" name="address" class="input">
            </div>

            {{-- DATE OF BIRTH --}}
            <div class="mb-4">
                <label for="birthDate">Date of Birth</label>
                <input type="date" name="birthDate" class="input">
            </div>

            {{-- EMAIL --}}
            <div class="mb-4">
                <label for="email">Email</label>
                <input type="text" name="email" class="input">
            </div>

            {{-- PASSWORD --}}
            <div class="mb-4">
                <label for="password">Password</label>
                <input type="password" name="password" class="input">
            </div>

            {{-- CONFIRM PASSWORD --}}
            <div class="mb-4">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" class="input">
            </div>

            {{-- SELECT TEAM --}}
            <div class="mb-4">
                <label for="team">Team</label>
                <select name="team" id="team" class="input">
                    <option value="" disabled selected>-- Select Team --</option>
                    <option value="alpha">Alpha</option>
                    <option value="bravo">Bravo</option>
                    <option value="charlie">Charlie</option>
                    <!-- Add more teams as needed -->
                </select>
            </div>

            {{-- SELECT POSITION --}}
            <div class="mb-10">
                <label for="position">Position</label>
                <select name="position" id="position" class="input">
                    <option value="" disabled selected>-- Select Position --</option>
                    <option value="firefighter">Firefighter</option>
                    <option value="team_leader">Team Leader</option>
                    <option value="driver">Driver</option>
                    <!-- Add more positions as needed -->
                </select>
            </div>

            {{-- SUBMIT BUTTON --}}
            <button class="btn">Create Account</button>
        </form>
    </div>
</x-layout>
