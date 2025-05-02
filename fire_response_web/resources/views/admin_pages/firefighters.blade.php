<x-dashboard>
    @section('title', 'Fire Emergency - BFP')
    <div class="container pt-6 ml-21">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Firefighters List</h1>
            </div>
        </header>

        <!-- 🔍 Search and 🏷️ Filter -->
        <form method="GET" class="flex flex-wrap gap-4 mb-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                class="border rounded-lg p-2 w-full md:w-1/3">

            <select name="team" class="border rounded-lg p-2 w-full md:w-1/4">
                <option value="">Filter by Team</option>
                @if (isset($teams) && count($teams) > 0)
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" {{ request('team') == $team->id ? 'selected' : '' }}>
                            {{ $team->teamName }}
                        </option>
                    @endforeach
                @endif
            </select>
            <!-- 🎖️ Filter by Rank -->
            <select name="rank_id" class="border rounded-lg p-2 w-full md:w-1/4">
                <option value="">Filter by Rank</option>
                @foreach ($ranks as $rank)
                    <option value="{{ $rank->id }}" {{ request('rank_id') == $rank->id ? 'selected' : '' }}>
                        {{ $rank->rank_name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                Apply
            </button>

            <a href="{{ route('admin.firefighters') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Reset
            </a>

            <!-- Register New Firefighter Button -->
            <a href="{{ route('admin.firefighters.register.form') }}"
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                Add Firefighter
            </a>
        </form>

        {{-- <h1 class="text-2xl font-bold mb-6">Firefighters List</h1> --}}

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($firefighters as $firefighter)
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <!-- 📸 Image Placeholder -->
                    <div class="h-40 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-500 text-6xl">👤</span>
                    </div>

                    <div class="p-6">
                        <!-- 📛 Name -->
                        <h2 class="text-xl font-semibold mb-2">
                            {{ $firefighter->user->userFirstName }} {{ $firefighter->user->userLastName }}
                        </h2>

                        <!-- 🎖️ Rank -->
                        <p class="text-gray-600 mb-1">
                            <strong>Rank:</strong> {{ $firefighter->rank->rank_name ?? 'No Rank' }}
                        </p>

                        <!-- 📧 Email -->
                        <p class="text-gray-600 mb-1">
                            <strong>Email:</strong> {{ $firefighter->user->email }}
                        </p>

                        <!-- 📞 Contact -->
                        <p class="text-gray-600 mb-1">
                            <strong>Contact:</strong> {{ $firefighter->user->userContactNumber }}
                        </p>

                        <!-- 🏷️ Team -->
                        <p class="text-gray-600 mb-1">
                            <strong>Team:</strong> {{ $firefighter->team->teamName ?? 'No Team' }}
                        </p>

                        <!-- 🎖️ Position -->
                        <p class="text-gray-600 mb-1">
                            <strong>Position:</strong> {{ $firefighter->position->position_name ?? 'No Position' }}
                        </p>


                        <!-- 🟢 Status -->
                        <p class="text-gray-600 mb-4">
                            <strong>Status:</strong>
                            @if (strtolower($firefighter->status) === 'off duty')
                                {{ $firefighter->status }}
                            @else
                                {{ $firefighter->team->status ?? 'No Status' }}
                            @endif
                        </p>

                        <!-- ✏️ Actions -->
                        <div class="flex justify-between">
                            <a href="" class="text-blue-500 hover:underline">Edit</a>

                            <form action="" method="POST" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-3 text-center text-gray-500">No firefighters found.</p>
            @endforelse
        </div>

        <!-- 📖 Pagination -->
        <div class="mt-6">
            {{ $firefighters->links() }}
        </div>
    </div>
</x-dashboard>
