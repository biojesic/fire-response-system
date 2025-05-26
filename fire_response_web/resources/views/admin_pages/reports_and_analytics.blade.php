<x-dashboard>
    @section('title', 'Fire Emergency - BFP')
    <div class="container w-270 pt-6 ml-20">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Reports and Analytics</h1>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Monthly Average Reports -->
            <div class="bg-white shadow p-4 rounded">
                <h3 class="text-xl font-semibold mb-4">Monthly Average Reports</h3>
                <canvas id="monthlyReportsChart"></canvas>
            </div>

            <!-- Total Number of Cases -->
            <div class="bg-white shadow p-4 rounded">
                <h3 class="text-xl font-semibold mb-4">Total Number of Cases</h3>
                <p class="text-3xl font-bold">{{ $totalCases }}</p>
            </div>

            <!-- Average Response Time -->
            <div class="bg-white shadow p-4 rounded">
                <h3 class="text-xl font-semibold mb-4">Average Response Time</h3>
                <p class="text-3xl font-bold">{{ number_format($averageResponseTime, 2) }} minutes</p>
            </div>

            <!-- False Alarm Counts -->
            <div class="bg-white shadow p-4 rounded">
                <h3 class="text-xl font-semibold mb-4">False Alarm Counts</h3>
                <p class="text-3xl font-bold">{{ $falseAlarms }}</p>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Monthly Reports Chart
        var ctx = document.getElementById('monthlyReportsChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line', // You can use 'bar', 'pie', etc.
            data: {
                labels: @json($monthlyReports->pluck('month')),
                datasets: [{
                    label: 'Total Reports',
                    data: @json($monthlyReports->pluck('total_reports')),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            }
        });
    </script>

</x-dashboard>
