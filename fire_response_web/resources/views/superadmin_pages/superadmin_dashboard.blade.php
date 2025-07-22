<x-dashboard>
    @section('title', 'Fire Emergency - BFP')

    <div class="container w-235 pt-6 ml-10">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-sm text-gray-500">Welcome back, {{ $position->position_name }} {{ $user->userFirstName }}!
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <input type="text" placeholder="Search..."
                        class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <div class="relative">
                    <button class="p-2 rounded-full bg-gray-200 text-gray-600 hover:bg-gray-300">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Incidents -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-600">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Incidents</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_incidents'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    @if ($stats['incident_change']['value'] > 0)
                        <span
                            class="{{ $stats['incident_change']['improved'] ? 'text-green-500' : 'text-red-500' }} font-medium">
                            {{ $stats['incident_change']['improved'] ? '↑' : '↓' }}
                            {{ $stats['incident_change']['value'] }}%
                        </span>
                    @else
                        <span class="text-gray-500">No change</span>
                    @endif
                    from last month
                </p>
            </div>

            <!-- Active Responders -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-600">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Active Responders</p>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ $stats['active_responders'] ?? 0 }}
                        </p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    @if (($stats['responder_change']['value'] ?? 0) > 0)
                        <span
                            class="{{ $stats['responder_change']['is_increase'] ? 'text-green-500' : 'text-red-500' }} font-medium">
                            {{ $stats['responder_change']['is_increase'] ? '↑' : '↓' }}
                            {{ $stats['responder_change']['value'] ?? 0 }}%
                        </span>
                    @else
                        <span class="text-gray-500">No change</span>
                    @endif
                    from last week
                </p>
            </div>

            <!-- Response Time -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-600">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Avg. Response Time</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['avg_response_time'] }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    @if (($stats['response_time_change']['value'] ?? 0) > 0)
                        <span
                            class="{{ $stats['response_time_change']['improved'] ? 'text-green-500' : 'text-red-500' }} font-medium">
                            {{ $stats['response_time_change']['improved'] ? '↑' : '↓' }}
                            {{ $stats['response_time_change']['value'] }}%
                        </span>
                        {{ $stats['response_time_change']['improved'] ? 'faster' : 'slower' }}
                    @else
                        <span class="text-gray-500">No change</span>
                    @endif
                    than last month
                </p>
            </div>

            <!-- USERS -->
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-600 relative">
                <!-- Unverified users notification badge -->
                @if (($stats['unverified_users'] ?? 0) > 0)
                    <span
                        class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center"
                        title="{{ $stats['unverified_users'] }} unverified users">
                        {{ $stats['unverified_users'] }}
                    </span>
                @endif

                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Active Civilian Users</p>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ number_format($stats['active_users'] ?? 0) }}
                        </p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                </div>
                <div class="flex justify-between items-baseline mt-2">
                    <p class="text-xs text-gray-500">
                        @if (isset($stats['active_users_change']['value']) && $stats['active_users_change']['value'] != 0)
                            <span
                                class="{{ ($stats['active_users_change']['value'] ?? 0) >= 0 ? 'text-green-500' : 'text-red-500' }} font-medium">
                                {{ ($stats['active_users_change']['value'] ?? 0) >= 0 ? '↑' : '↓' }}
                                {{ abs($stats['active_users_change']['value'] ?? 0) }}%
                            </span>
                        @else
                            <span class="text-gray-500">No change</span>
                        @endif
                        from last week
                    </p>
                    {{-- @if (($stats['unverified_users'] ?? 0) > 0)
                        <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">
                            {{ $stats['unverified_users'] }} to verify
                        </span>
                    @endif --}}
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Incident Trends -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Incident Trends</h2>
                    <select class="border rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option>Monthly</option>
                        <option>Weekly</option>
                        <option>Daily</option>
                    </select>
                </div>
                <div class="bg-gray-100 rounded-lg h-64 flex items-center justify-center">
                    <!-- Chart placeholder - replace with actual chart component -->
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Incident trends chart</p>
                    </div>
                </div>
            </div>

            <!-- Team Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Team Status</h2>
                    <button class="text-sm text-red-600 hover:text-red-800">View All</button>
                </div>
                <div class="space-y-4">
                    @for ($i = 0; $i < 3; $i++)
                        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-800">Team Alpha {{ $i + 1 }}</h3>
                                    <p class="text-sm text-gray-500">5 members</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">On Duty</span>
                        </div>
                    @endfor
                    @for ($i = 0; $i < 2; $i++)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="p-2 rounded-full bg-gray-100 text-gray-600 mr-3">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-800">Team Bravo {{ $i + 1 }}</h3>
                                    <p class="text-sm text-gray-500">4 members</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Standby</span>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        {{-- BOTTOM SECTION --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Civilian Users Trends -->
            <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Civilian Users Trends</h2>
                    <select id="civilian-trends-selector"
                        class="border rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="status">By Status</option>
                        <option value="verification">Verification Trends</option>
                        <option value="monthly">Monthly Growth</option>
                    </select>
                </div>

                <!-- Charts Container -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Status Distribution Pie Chart -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">User Status Distribution</h3>
                        <canvas id="status-pie-chart" class="h-64"></canvas>
                    </div>

                    <!-- Monthly Growth Line Chart -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Monthly User Growth</h3>
                        <canvas id="growth-line-chart" class="h-64"></canvas>
                    </div>

                    <!-- Verification Trends Bar Chart -->
                    <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Verification Activity (Last 30 Days)</h3>
                        <canvas id="verification-bar-chart" class="h-64"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Alerts -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Recent Alerts</h2>
                    <button class="text-sm text-red-600 hover:text-red-800">View All</button>
                </div>
                <div class="space-y-4">
                    @for ($i = 0; $i < 5; $i++)
                        <div class="flex items-start pb-3 border-b border-gray-100">
                            <div class="p-2 rounded-full bg-red-100 text-red-600 mr-3">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-800">Fire Incident Reported</h3>
                                <p class="text-sm text-gray-500">Barangay 123, Quezon City</p>
                                <p class="text-xs text-gray-400 mt-1">10 minutes ago</p>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all charts
            function initCharts() {
                // Status Distribution Pie Chart
                const statusChartEl = document.getElementById('status-pie-chart');
                if (statusChartEl && statusChartEl.getContext) { // Check if element exists and is canvas
                    const statusCtx = statusChartEl.getContext('2d');
                    new Chart(statusCtx, {
                        type: 'pie',
                        data: {
                            labels: ['Active', 'Unverified', 'Rejected', 'Inactive'],
                            datasets: [{
                                data: [5, 0, 0, 1], // Replace with your actual data
                                backgroundColor: [
                                    '#10B981', '#F59E0B', '#EF4444', '#6B7280'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                } else {
                    console.error('Status chart element not found or not a canvas');
                }

                // Monthly Growth Line Chart
                const growthChartEl = document.getElementById('growth-line-chart');
                if (growthChartEl && growthChartEl.getContext) {
                    const growthCtx = growthChartEl.getContext('2d');
                    new Chart(growthCtx, {
                        type: 'line',
                        data: {
                            labels: ["May", "Jun"], // Replace with your actual data
                            datasets: [{
                                label: 'New Civilian Users',
                                data: [4, 2], // Replace with your actual data
                                borderColor: '#3B82F6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                // Verification Trends Bar Chart
                const verificationChartEl = document.getElementById('verification-bar-chart');
                if (verificationChartEl && verificationChartEl.getContext) {
                    const verificationCtx = verificationChartEl.getContext('2d');
                    new Chart(verificationCtx, {
                        type: 'bar',
                        data: {
                            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                            datasets: [{
                                    label: 'Verified',
                                    data: [0, 0, 0, 0], // Replace with your actual data
                                    backgroundColor: '#10B981'
                                },
                                {
                                    label: 'Rejected',
                                    data: [0, 0, 0, 0], // Replace with your actual data
                                    backgroundColor: '#EF4444'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    stacked: true
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            }

            // Initialize charts when DOM is loaded
            initCharts();

            // Chart selector functionality
            const trendSelector = document.getElementById('civilian-trends-selector');
            if (trendSelector) {
                trendSelector.addEventListener('change', function(e) {
                    const value = e.target.value;
                    // Hide all chart containers first
                    document.querySelectorAll('[id$="-chart"]').forEach(el => {
                        el.closest('div.bg-gray-50').classList.add('hidden');
                    });

                    // Show selected chart
                    let chartToShow;
                    switch (value) {
                        case 'status':
                            chartToShow = document.querySelector('#status-pie-chart').closest(
                                'div.bg-gray-50');
                            break;
                        case 'verification':
                            chartToShow = document.querySelector('#verification-bar-chart').closest(
                                'div.bg-gray-50');
                            break;
                        case 'monthly':
                            chartToShow = document.querySelector('#growth-line-chart').closest(
                                'div.bg-gray-50');
                            break;
                    }

                    if (chartToShow) chartToShow.classList.remove('hidden');
                });
            }
        });
    </script>
</x-dashboard>
