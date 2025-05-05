<x-dashboard>
    @section('title', 'Teams Management - BFP')
    <div class="container w-270 p-6 ml-21">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Teams Management</h1>
            </div>
        </header>

        <div class="p-6">
            {{-- Teams List --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($teams as $team)
                    <div class="bg-white shadow-md rounded-lg overflow-hidden flex flex-col justify-between">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold mb-2">{{ $team->teamName }}</h2>
                            <p class="text-gray-600 mb-4">Status: <span class="font-semibold">{{ $team->status }}</span>
                            </p>

                            <h3 class="font-semibold mb-2">Members:</h3>
                            <ul class="mb-4 max-h-40 overflow-y-auto">
                                @forelse ($team->firefighters as $firefighter)
                                    <li class="flex justify-between items-center border-b py-2">
                                        <span>
                                            {{ $firefighter->user->userFirstName }}
                                            {{ $firefighter->user->userLastName }}
                                            <small
                                                class="text-gray-500">({{ $firefighter->position->position_name ?? 'No Position' }})</small>
                                        </span>
                                        <form
                                            action="{{ route('teams.removeFirefighter', [$team->id, $firefighter->id]) }}"
                                            method="POST"
                                            onsubmit="return confirm('Remove this firefighter from team?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="text-red-500 text-sm hover:underline">Remove</button>
                                        </form>
                                    </li>
                                @empty
                                    <li class="text-gray-500 italic">No members assigned yet.</li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- Assign Personnel Button --}}
                        <div class="p-4 border-t bg-gray-50">
                            <form
                                action="{{ route('teams.assign', ['team' => $team->id, 'firefighter' => 'firefighterId']) }}"
                                method="POST">
                                @csrf
                                <label for="firefighter" class="block mb-2">Select Firefighter:</label>
                                <select id="firefighter" name="firefighter_id" class="w-full p-2 border rounded mb-4">
                                    @foreach ($availableFirefighters as $firefighter)
                                        <option value="{{ $firefighter->id }}">
                                            {{ $firefighter->user->userFirstName }}
                                            {{ $firefighter->user->userLastName }}
                                            ({{ $firefighter->position->position_name ?? 'No Position' }})
                                        </option>
                                    @endforeach
                                </select>

                                <button type="submit"
                                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 w-full">
                                    Assign
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No teams available.</p>
                @endforelse
            </div>

            {{-- Unassigned Firefighters --}}
            <div class="mt-10">
                <h2 class="text-2xl font-bold mb-4">Unassigned Firefighters</h2>
                <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($availableFirefighters as $firefighter)
                        <li class="bg-gray-100 p-4 rounded shadow-sm">
                            {{ $firefighter->user->userFirstName }} {{ $firefighter->user->userLastName }}
                            <small
                                class="text-gray-500">({{ $firefighter->position->position_name ?? 'No Position' }})</small>
                        </li>
                    @empty
                        <li class="text-gray-500">No unassigned firefighters.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-dashboard>
