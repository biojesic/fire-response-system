<x-dashboard>
    @section('title', 'Fire Report Details')
    <div class="container pt-6 ml-21">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Fire Report Details</h1>
            </div>
        </header>

        <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
            <h2 class="text-xl font-semibold mb-2">{{ $report->location }}</h2>
            <p><strong>Date:</strong> {{ $report->created_at->format('M d, Y') }}</p>
            <p><strong>Description:</strong> {{ $report->description }}</p>
            <p><strong>Fire Station:</strong> {{ $report->fireStation->firestationName ?? 'No Fire Station' }}</p>
            <p><strong>Additional Details:</strong> {{ $report->additional_details ?? 'N/A' }}</p>
        </div>

        <a href="{{ route('admin.fire_reports') }}" class="text-blue-500 hover:underline mt-4">Back to Fire Reports</a>
    </div>
</x-dashboard>
