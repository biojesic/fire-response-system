<x-dashboard>
    @section('title', 'Fire Emergency - BFP')
    <div class="container w-230 pt-6 ml-45">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Progress Real Time Report</h1>
            </div>
        </header>

        <div class="card">
            <form action="{{ route('admin.firefighters.register') }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf

                {{-- FIRST NAME --}}
                <div class="mb-4">
                    <label for="userFirstName">Name of Fire Station or Sub-Station Responding</label>
                    <input type="text" name="userFirstName" value="{{ old('userFirstName') }}"
                        class="input @error('userFirstName') ring-red-500 @enderror">
                    @error('userFirstName')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- LAST NAME --}}
                <div class="mb-4">
                    <label for="userLastName">Type of Incident</label>
                    <input type="text" name="userLastName" value="{{ old('userLastName') }}"
                        class="input @error('userLastName') ring-red-500 @enderror">
                    @error('userLastName')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ADDRESS --}}
                <div class="mb-4 col-span-2">
                    <label for="userAddress">Exact Location</label>
                    <input type="text" name="userAddress" value="{{ old('userAddress') }}"
                        class="input @error('userAddress') ring-red-500 @enderror">
                    @error('userAddress')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- EMAIL --}}
                <div class="mb-4">
                    <label for="email">Responding Team</label>
                    <input type="text" name="email" value="{{ old('email') }}"
                        class="input @error('email') ring-red-500 @enderror">
                    @error('email')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- DATE OF BIRTH --}}
                <div class="mb-4">
                    <label for="userBirthDate">Time and Date Reported</label>
                    <input type="date" name="userBirthDate" value="{{ old('userBirthDate') }}"
                        class="input @error('userBirthDate') ring-red-500 @enderror">
                    @error('userBirthDate')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- EMAIL --}}
                <div class="mb-4">
                    <label for="email">Involved</label>
                    <input type="text" name="email" value="{{ old('email') }}"
                        class="input @error('email') ring-red-500 @enderror">
                    @error('email')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CONTACT NUMBER --}}
                <div class="mb-4">
                    <label for="userContactNumber">Time of Arrival</label>
                    <input type="text" name="userContactNumber" value="{{ old('userContactNumber') }}"
                        class="input @error('userContactNumber') ring-red-500 @enderror">
                    @error('userContactNumber')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CONTACT NUMBER --}}
                <div class="mb-4">
                    <label for="userContactNumber">Fire out</label>
                    <input type="text" name="userContactNumber" value="{{ old('userContactNumber') }}"
                        class="input @error('userContactNumber') ring-red-500 @enderror">
                    @error('userContactNumber')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CONTACT NUMBER --}}
                <div class="mb-4">
                    <label for="userContactNumber">Ground Commander</label>
                    <input type="text" name="userContactNumber" value="{{ old('userContactNumber') }}"
                        class="input @error('userContactNumber') ring-red-500 @enderror">
                    @error('userContactNumber')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                {{-- SELECT TEAM --}}
                {{-- <div class="mb-4">
                            <label for="teamId">Team</label>
                            <select name="teamId" id="teamId" class="input @error('teamId') ring-red-500 @enderror">
                                <option value="" disabled {{ old('teamId') ? '' : 'selected' }}>-- Select Team --
                                </option>
                                @foreach ($teams as $team)
                                    <option value="{{ $team->id }}"
                                        {{ old('teamId') == $team->id ? 'selected' : '' }}>
                                        {{ $team->teamName }}
                                    </option>
                                @endforeach
                            </select>
                            @error('teamId')
                                <p class="error">{{ $message }}</p>
                            @enderror
                        </div> --}}



                {{-- SUBMIT BUTTON --}}
                <div class="col-span-2 w-110 flex justify-center ml-57">
                    <button class="btn">Submit Report</button>
                </div>
            </form>
        </div>

    </div>
</x-dashboard>
