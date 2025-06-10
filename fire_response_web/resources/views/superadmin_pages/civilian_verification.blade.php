<x-dashboard>
    @section('title', 'Unverified Civilians - BFP')
    <div class="container w-255 pt-6 ml-21">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Unverified Civilians</h1>
        </header>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Name</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Email</th>
                        <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($unverifiedCivilians as $civilian)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $civilian->userFirstName }} {{ $civilian->userLastName }}</td>
                            <td class="px-4 py-2">{{ $civilian->email }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('civilian.verification.show', $civilian->id) }}"
                                    class="text-blue-500 hover:underline">View Full Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">No unverified civilians
                                found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-dashboard>
