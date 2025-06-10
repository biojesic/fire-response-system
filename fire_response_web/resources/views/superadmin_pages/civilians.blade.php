<x-dashboard>
    @section('title', 'Fire Emergency - BFP')
    <div class="container w-255 pt-6 ml-21">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Civilians</h1>
            </div>
        </header>

        <!-- 🔍 Search and 🏷️ Filter -->
        <form method="GET" class="flex flex-wrap gap-4 mb-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                class="border rounded-lg p-2 w-full md:w-1/3">

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                Apply
            </button>

            <a href="" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Reset
            </a>

            <a href="{{ route('civilians.verificationpage') }}"
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                For Verification
            </a>
        </form>

        <!-- Civilians Table -->
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="text-left bg-gray-100">
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">Name</th>
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">Email</th>
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">Reports Made</th>
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">False Alarms</th>
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">Status</th>
                    <th class="py-3 px-6 text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($civilians as $civilian)
                    <tr class="border-b">
                        <td class="py-3 px-6 text-sm text-gray-700">{{ $civilian->userFirstName }}
                            {{ $civilian->userLastName }}</td>
                        <td class="py-3 px-6 text-sm text-gray-700">{{ $civilian->email }}</td>
                        <td class="py-3 px-6 text-sm text-gray-700">{{ $civilian->reports_count }}</td>
                        <td class="py-3 px-6 text-sm text-gray-700">{{ $civilian->false_alarms_count }}</td>
                        <td class="py-3 px-6 text-sm text-gray-700">
                            <span
                                class="bg-{{ $civilian->userStatus == 'Rejected' ? 'red' : ($civilian->status == 'Inactive' ? 'yellow' : 'green') }}-500 text-white px-2 py-1 rounded">
                                {{ ucfirst($civilian->userStatus) }}
                            </span>

                        </td>
                        <td class="py-3 px-6 text-sm text-gray-700">
                            <a href="{{ route('civilians.show', $civilian->id) }}"
                                class="text-blue-500 hover:text-blue-700">View</a> |
                            <a href="{{ route('civilians.updateStatus', $civilian->id) }}"
                                class="text-green-500 hover:text-green-700">Update Status</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $civilians->links() }}
        </div>

    </div>
</x-dashboard>
