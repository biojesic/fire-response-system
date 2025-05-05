<x-dashboard>
    @section('title', 'Fire Emergency - BFP')
    <div class="container w-270 pt-6 ml-21">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Equipment List</h1>
            </div>
        </header>

        <form method="GET" class="flex flex-wrap gap-4 mb-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by equipment name..."
                class="border rounded-lg p-2 w-full md:w-1/3">

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                Apply
            </button>

            <a href="{{ route('admin.equipment.list') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Reset
            </a>

            <!-- Add New Equipment Button -->
            <a href="{{ route('admin.equipment.create') }}"
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                Add New Equipment
            </a>
        </form>

        <!-- 🔄 Equipment List Table -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($equipments as $equipment)
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold mb-2">{{ $equipment->name }}</h2>

                        <!-- Show Quantity -->
                        <p class="text-gray-600 mb-4">
                            <strong>Quantity:</strong> {{ $equipment->quantities }}
                        </p>

                        {{-- <p class="text-gray-600 mb-4">
                            <strong>Assigned Firefighters:</strong>
                            @foreach ($equipment->firefighters as $firefighter)
                                <div>
                                    <span>{{ $firefighter->user->userFirstName }}
                                        {{ $firefighter->user->userLastName }}</span>
                                </div>
                            @endforeach
                        </p> --}}


                        <!-- ✏️ Edit & 🗑️ Delete Actions -->
                        @if (auth()->user()->firefighter->position->position_name == 'Admin')
                            <div class="flex justify-between mt-5">
                                <a href="{{ route('admin.equipment.edit', $equipment->id) }}"
                                    class="text-blue-500 hover:underline">Edit</a>

                                <form action="{{ route('admin.equipment.destroy', $equipment->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this equipment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">Delete</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="col-span-3 text-center text-gray-500">No equipment found.</p>
            @endforelse
        </div>

        <!-- 📖 Pagination -->
        <div class="mt-6">
            {{ $equipments->links() }}
        </div>
    </div>
</x-dashboard>
