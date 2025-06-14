<x-dashboard>
    @section('title', 'Fire Emergency - BFP')
    <div class="container w-240 pt-6 ml-12">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Barangays</h1>
            </div>
        </header>

        <form method="GET" class="flex flex-wrap gap-4 mb-6">
            <!-- 🔍 Search -->
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by barangay name..."
                class="border rounded-lg p-2 w-full md:w-1/3">

            <!-- 🏙️ City / Municipality Filter -->
            <select name="lgu" class="border rounded-lg p-2 w-full md:w-1/4">
                <option value="">All Cities/Municipalities</option>
                @foreach ($lgus as $lgu)
                    <option value="{{ $lgu->id }}" {{ request('lgu') == $lgu->id ? 'selected' : '' }}>
                        {{ $lgu->name }}
                    </option>
                @endforeach
            </select>

            <!-- 🚒 Fire Station Filter -->
            <select name="fire_station" class="border rounded-lg p-2 w-full md:w-1/4">
                <option value="">All Fire Stations</option>
                @foreach ($fireStations as $station)
                    <option value="{{ $station->id }}" {{ request('fire_station') == $station->id ? 'selected' : '' }}>
                        {{ $station->firestationName }}
                    </option>
                @endforeach
            </select>

            <!-- Second Line Starts Here -->
            <div class="w-full flex flex-wrap gap-4"> <!-- New container div -->
                <!-- ✅ Apply Filters -->
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Apply
                </button>

                <!-- 🔄 Reset Filters -->
                <a href="{{ route('barangay.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Reset
                </a>

                <a href="{{ route('barangay.verification') }}"
                    class="relative bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                    For Verification
                    @if ($pendingCount > 0)
                        <span
                            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
            </div>
        </form>


        <!-- Barangays Table -->
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="text-left bg-gray-100">
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">Name</th>
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">Reports Made</th>
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">City/Municipality</th>
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">Fire Station</th>
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">Status</th>
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($barangays as $barangay)
                    <tr class="border-b">
                        <td class="py-3 px-6 text-sm text-gray-700">{{ $barangay->barangay_name }}</td>
                        <td class="py-3 px-6 text-sm text-gray-700">{{ $barangay->reports_count }}</td>
                        <td class="py-3 px-6 text-sm text-gray-700">{{ $barangay->lgu->name }}</td>
                        <td class="py-3 px-6 text-sm text-gray-700">{{ $barangay->fireStation->firestationName }}</td>
                        <td class="py-3 px-6 text-sm text-gray-700">
                            <span
                                class="bg-{{ $barangay->brgy_status == 'Rejected' ? 'red' : ($barangay->brgy_status == 'Inactive' ? 'yellow' : 'green') }}-500 text-white px-2 py-1 rounded">
                                {{ ucfirst($barangay->brgy_status) }}
                            </span>

                        </td>
                        <td class="py-3 px-6 text-sm text-gray-700">
                            <a href="{{ route('barangay.show', $barangay->id) }}"
                                class="text-blue-500 hover:text-blue-700">View</a> |
                            <a href="" class="text-green-500 hover:text-green-700">Update</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $barangays->links() }}
        </div>

    </div>
</x-dashboard>
