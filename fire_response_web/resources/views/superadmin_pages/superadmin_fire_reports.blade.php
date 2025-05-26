<x-dashboard>
    @section('title', 'Fire Emergency - BFP')
    <div class="container w-270 pt-6 ml-21">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Fire Reports</h1>
            </div>
        </header>

        <!-- Table for displaying fire reports -->
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-2 px-4 text-left">Location</th>
                        <th class="py-2 px-4 text-left">Date</th>
                        <th class="py-2 px-4 text-left">Status</th>
                        <th class="py-2 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($fireReports as $report)
                        <tr class="border-b">
                            <td class="py-2 px-4">{{ $report->location }}</td>
                            <td class="py-2 px-4">{{ $report->created_at->format('M d, Y') }}</td>
                            <td class="py-2 px-4">{{ $report->status }}</td>
                            <td class="py-2 px-4">
                                <a href="{{ route('admin.fire_reports.show', $report->id) }}"
                                    class="text-blue-500 hover:underline">View Report</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-2 px-4 text-center text-gray-500">No fire reports available for
                                your station.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $fireReports->links() }}
        </div>
    </div>
</x-dashboard>
