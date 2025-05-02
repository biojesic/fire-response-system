<x-dashboard>
    @section('title', 'Fire Emergency - Register Firefighter')

    <div class="container pt-6 ml-21">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Register A New Firefighter</h1>
            </div>
        </header>

        <div class="card">
            <form action="{{ route('admin.firefighters.register.form') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf

                {{-- FIRST NAME --}}
                <div class="mb-4">
                    <label for="userFirstName">First Name</label>
                    <input type="text" name="userFirstName" value="{{ old('userFirstName') }}"
                        class="input @error('userFirstName') ring-red-500 @enderror">
                    @error('userFirstName')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- LAST NAME --}}
                <div class="mb-4">
                    <label for="userLastName">Last Name</label>
                    <input type="text" name="userLastName" value="{{ old('userLastName') }}"
                        class="input @error('userLastName') ring-red-500 @enderror">
                    @error('userLastName')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ADDRESS --}}
                <div class="mb-4">
                    <label for="userAddress">Address</label>
                    <input type="text" name="userAddress" value="{{ old('userAddress') }}"
                        class="input @error('userAddress') ring-red-500 @enderror">
                    @error('userAddress')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DATE OF BIRTH --}}
                <div class="mb-4">
                    <label for="userBirthDate">Date of Birth</label>
                    <input type="date" name="userBirthDate" value="{{ old('userBirthDate') }}"
                        class="input @error('userBirthDate') ring-red-500 @enderror">
                    @error('userBirthDate')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- EMAIL --}}
                <div class="mb-4">
                    <label for="email">Email</label>
                    <input type="text" name="email" value="{{ old('email') }}"
                        class="input @error('email') ring-red-500 @enderror">
                    @error('email')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CONTACT NUMBER --}}
                <div class="mb-4">
                    <label for="userContactNumber">Contact Number</label>
                    <input type="text" name="userContactNumber" value="{{ old('userContactNumber') }}"
                        class="input @error('userContactNumber') ring-red-500 @enderror">
                    @error('userContactNumber')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- PASSWORD --}}
                <div class="mb-4">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="input @error('password') ring-red-500 @enderror">
                    @error('password')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CONFIRM PASSWORD --}}
                <div class="mb-4">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="input">
                </div>

                {{-- PERSONAL EQUIPMENT --}}
                <div class="mb-4">
                    <label>Personal Equipment</label><br>
                    @foreach ($equipmentOptions as $equipment)
                        <label>
                            <input type="checkbox" name="personalEquipment[]" value="{{ $equipment->id }}"
                                {{ in_array($equipment->id, old('personalEquipment', [])) ? 'checked' : '' }}>
                            {{ $equipment->name }}
                        </label><br>
                    @endforeach
                </div>

                {{-- SELECT TEAM --}}
                <div class="mb-4">
                    <label for="teamId">Team</label>
                    <select name="teamId" id="teamId" class="input @error('teamId') ring-red-500 @enderror">
                        <option value="" disabled {{ old('teamId') ? '' : 'selected' }}>-- Select Team --
                        </option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" {{ old('teamId') == $team->id ? 'selected' : '' }}>
                                {{ $team->teamName }}
                            </option>
                        @endforeach
                    </select>
                    @error('teamId')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SELECT RANK --}}
                <div class="mb-4">
                    <label for="rank_id">Rank</label>
                    <select name="rank_id" id="rank_id" class="input @error('rank_id') ring-red-500 @enderror">
                        <option value="" disabled {{ old('rank_id') ? '' : 'selected' }}>-- Select Rank --
                        </option>
                        @foreach ($ranks as $rank)
                            <option value="{{ $rank->id }}" {{ old('rank_id') == $rank->id ? 'selected' : '' }}>
                                {{ $rank->rank_name }}
                            </option>
                        @endforeach
                    </select>

                    @error('rank_id')
                        <p class="error">{{ $message }}</p>
                    @enderror

                    {{-- SELECT POSITION --}}
                    <div class="mb-10">
                        <label for="position_id">Position</label>
                        <select name="position_id" id="position_id"
                            class="input @error('position_id') ring-red-500 @enderror">
                            <option value="" disabled {{ old('position_id') ? '' : 'selected' }}>-- Select
                                Position
                                --</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}"
                                    {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                    {{ $position->position_name }}
                                </option>
                            @endforeach
                        </select>

                        @error('position_id')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <div class="col-span-2 flex justify-center">
                        <button class="btn">Create Account</button>
                    </div>
            </form>
        </div>
    </div>
</x-dashboard>
