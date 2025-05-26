<x-layout>
    @section('title', 'Fire Emergency - BFP')

    <div class="container w-270 pt-6 ml-20">
        <!-- Header Section -->
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Barangay Dashboard</h1>
                <p class="text-sm text-gray-500">Barangay {{ $barangayName }}</p>
            </div>
        </header>

        <!-- Dashboard Summary Cards Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Total Fire Reports Card -->
            <div class="bg-white shadow p-4 rounded">
                <h3 class="text-xl font-semibold mb-4">Total Fire Reports</h3>
                <p class="text-3xl font-bold">{{ $totalReports }}</p>
            </div>

            <!-- New Fire Reports Card -->
            <div class="bg-white shadow p-4 rounded">
                <h3 class="text-xl font-semibold mb-4">New Reports (Last 24 Hours)</h3>
                <p class="text-3xl font-bold">{{ $newReports }}</p>
            </div>

            <!-- Resolved Fire Reports Card -->
            <div class="bg-white shadow p-4 rounded">
                <h3 class="text-xl font-semibold mb-4">Resolved Fire Reports</h3>
                <p class="text-3xl font-bold">{{ $resolvedReports }}</p>
            </div>

            <!-- False Alarm Reports Card -->
            <div class="bg-white shadow p-4 rounded">
                <h3 class="text-xl font-semibold mb-4">False Alarm Reports</h3>
                <p class="text-3xl font-bold">{{ $falseAlarms }}</p>
            </div>

            <!-- Active Fires Card -->
            <div class="bg-white shadow p-4 rounded">
                <h3 class="text-xl font-semibold mb-4">Active Fires</h3>
                <p class="text-3xl font-bold">{{ $activeFires }}</p>
            </div>

            <!-- Average Response Time Card -->
            <div class="bg-white shadow p-4 rounded">
                <h3 class="text-xl font-semibold mb-4">Average Response Time</h3>
                <p class="text-3xl font-bold">{{ number_format($averageResponseTime, 2) }} mins</p>
            </div>
        </div>

        <!-- Recent Fire Reports Section -->
        <div class="bg-white shadow p-4 rounded mt-6">
            <h3 class="text-xl font-semibold mb-4">Recent Fire Reports</h3>
            <ul>
                @foreach ($recentReports as $report)
                    <li class="mb-2">
                        <strong>{{ $report->location }}</strong> - Status: {{ $report->status }} | Reported at:
                        {{ $report->created_at->format('Y-m-d H:i') }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</x-layout>
