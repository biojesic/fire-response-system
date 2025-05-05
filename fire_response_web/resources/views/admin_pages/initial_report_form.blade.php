<x-dashboard>
    @section('title', 'Fire Emergency - BFP')
    <div class="container w-230 pt-6 ml-45">

        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Initial Real Time Report</h1>
            </div>
        </header>

        <div class="card">
            <form action="{{ route('fire-reports.initial', $report->id) }}" method="POST"
                class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf
                <input type="hidden" name="stage" value="initial">
                <input type="hidden" name="sender" value="{{ $senderInfo }}">

                {{-- Name of Fire Station or Sub-Station --}}
                <div class="mb-4">
                    <label for="firestation_name">Name of Fire Station or Sub-Station Responding</label>
                    <input type="text" name="firestation_name" value="{{ $firestationName }}"
                        class="input @error('firestation_name') ring-red-500 @enderror">
                    @error('firestation_name')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Type of Incident --}}
                <div class="mb-4">
                    <label for="incident_type">Type of Incident</label>
                    <input type="text" name="incident_type" value="{{ old('incident_type') }}"
                        class="input @error('incident_type') ring-red-500 @enderror">
                    @error('incident_type')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Exact Location --}}
                <div class="mb-4 col-span-2">
                    <label for="location">Exact Location</label>
                    <input type="text" name="location" value="{{ $location }}"
                        class="input @error('location') ring-red-500 @enderror">
                    @error('location')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Responding Team --}}
                <div class="mb-4">
                    <label for="responding_team">Responding Team</label>
                    <input type="text" name="responding_team" value="{{ old('responding_team') }}"
                        class="input @error('responding_team') ring-red-500 @enderror">
                    @error('responding_team')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Time and Date Reported --}}
                <div class="mb-4">
                    <label for="time_and_date">Time and Date Reported</label>
                    <input type="datetime-local" name="time_and_date"
                        value="{{ $timeAndDateReported->format('Y-m-d\TH:i') }}"
                        class="input @error('time_and_date') ring-red-500 @enderror">
                    @error('time_and_date')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>


                {{-- Involved --}}
                <div class="mb-4">
                    <label for="involved">Involved</label>
                    <input type="text" name="involved" value="{{ old('involved') }}"
                        class="input @error('involved') ring-red-500 @enderror">
                    @error('involved')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Time of Arrival --}}
                <div class="mb-4">
                    <label for="time_of_arrival">Time of Arrival</label>
                    <input type="text" name="time_of_arrival" value="{{ old('time_of_arrival') }}"
                        class="input @error('time_of_arrival') ring-red-500 @enderror">
                    @error('time_of_arrival')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fire out --}}
                <div class="mb-4">
                    <label for="fire_out">Fire out</label>
                    <input type="text" name="fire_out" value="{{ old('fire_out') }}"
                        class="input @error('fire_out') ring-red-500 @enderror">
                    @error('fire_out')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ground Commander --}}
                <div class="mb-4">
                    <label for="ground_commander">Ground Commander</label>
                    <input type="text" name="ground_commander" value="{{ old('ground_commander') }}"
                        class="input @error('ground_commander') ring-red-500 @enderror">
                    @error('ground_commander')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- SUBMIT BUTTON --}}
                <div class="col-span-2 w-110 flex justify-center ml-57">
                    <button class="btn">Submit Report</button>
                </div>
            </form>
        </div>
    </div>

</x-dashboard>
