<x-dashboard>
    @section('title', 'Fire Emergency - BFP')
    <div class="">
        <main class="flex-1 p-6 pt-0">
            <main class="flex-1 p-6 ml-10 ">

                <header class="flex justify-between items-center border-b pb-4 mb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
                        <p class="text-sm text-gray-500">Welcome back, {{ $position->position_name }}
                            {{ $user->userFirstName }}!</p>
                    </div>
                </header>

                <!-- Parent Container for 3 Columns -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Active Incidents -->
                    <section class="mt-6">
                        <h3 class="text-xl font-semibold mb-4">Active Incidents</h3>

                        <!-- Tabs Navigation -->
                        <div x-data="{ activeTab: 'pending' }">
                            <ul class="flex border-b" role="tablist">
                                <li class="-mb-px mr-1">
                                    <button
                                        class="inline-block py-2 px-4 text-blue-500 font-semibold border-l border-t border-r rounded-t"
                                        :class="{
                                            'bg-blue-500 text-white': activeTab === 'pending',
                                            'bg-white': activeTab !== 'pending'
                                        }"
                                        @click="activeTab = 'pending'" type="button" role="tab">
                                        Pending
                                    </button>
                                </li>
                                <li class="mr-1">
                                    <button
                                        class="inline-block py-2 px-4 text-blue-500 font-semibold border-l border-t border-r rounded-t"
                                        :class="{
                                            'bg-blue-500 text-white': activeTab === 'onResponse',
                                            'bg-white': activeTab !== 'onResponse'
                                        }"
                                        @click="activeTab = 'onResponse'" type="button" role="tab">
                                        On Response
                                    </button>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content mt-4">
                                <!-- Pending Tab Content -->
                                <div x-show="activeTab === 'pending'" role="tabpanel">
                                    @if ($pendingFireReports->isEmpty())
                                        <p>No active incidents at the moment.</p>
                                    @else
                                        <!-- Scrollable Parent Container for Pending Incidents -->
                                        <div class="bg-white p-4 rounded shadow h-96 overflow-y-auto">
                                            @foreach ($pendingFireReports as $incident)
                                                <div class="bg-gray-100 p-4 rounded mb-4">
                                                    <h4 class="font-semibold">{{ $incident->description }} - Status:
                                                        {{ $incident->status }}</h4>
                                                    <p>Location: {{ $incident->location }}</p>
                                                    <form action="{{ route('admin.dispatch', $incident->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        <!-- Assign Teams -->
                                                        <div class="mt-4">
                                                            <label for="teams" class="font-semibold">Assign Teams to
                                                                this Incident:</label>
                                                            <div class="space-x-2 mt-2">
                                                                @foreach ($teams as $team)
                                                                    <button type="button"
                                                                        class="team-button bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-2"
                                                                        data-team-id="{{ $team->id }}"
                                                                        onclick="toggleTeamSelection({{ $team->id }})">
                                                                        {{ $team->teamName }}
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                        </div>

                                                        <!-- Hidden input to store selected team IDs -->
                                                        <input type="hidden" name="selected_teams" id="selected_teams">
                                                        <button type="submit"
                                                            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mt-4">
                                                            Dispatch Selected Teams
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- On Response Tab Content -->
                                <div x-show="activeTab === 'onResponse'" role="tabpanel">
                                    @if ($onResponseFireReports->isEmpty())
                                        <p>No fire reports are currently being responded to.</p>
                                    @else
                                        <!-- Scrollable Parent Container for On Response Incidents -->
                                        <div class="bg-white p-4 rounded shadow h-96 overflow-y-auto">
                                            @foreach ($onResponseFireReports as $onResponse)
                                                <div class="bg-gray-100 p-4 rounded mb-4">
                                                    <h4 class="font-semibold">{{ $onResponse->description }} - Status:
                                                        {{ $onResponse->status }}</h4>
                                                    <p>Location: {{ $onResponse->location }}</p>
                                                    <form action="{{ route('markAsContained', $onResponse->id) }}"
                                                        method="POST" onsubmit="markAsContained(event, this)">
                                                        @csrf
                                                        <!-- Mark as Contained -->
                                                        <button type="submit"
                                                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mt-4">
                                                            Mark as Contained
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Center Column: Standby Teams and New Reports -->
                    <div class="space-y-6">
                        <!-- Standby Teams -->
                        <section class="mt-6">
                            <h3 class="text-xl font-semibold mb-4">Standby Teams</h3>
                            <div class="space-y-4">
                                @foreach ($teams as $team)
                                    <div class="bg-white p-4 rounded shadow">
                                        <h4 class="font-semibold">{{ $team->teamName }}</h4>
                                        <p>On-Duty Firefighters:</p>
                                        <ul>
                                            @foreach ($team->firefighters as $firefighter)
                                                <li>{{ $firefighter->user->userLastName }} (Status:
                                                    {{ $firefighter->status }})</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </section>

                        <!-- New Reports Notification -->
                        <section class="mt-6">
                            <h3 class="text-xl font-semibold mb-4">New Reports</h3>
                            @if ($newReports->isEmpty())
                                <p>No new reports in the last 24 hours.</p>
                            @else
                                <ul>
                                    @foreach ($newReports as $report)
                                        <li>Location: {{ $report->location }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </section>
                    </div>

                    <!-- Right Column: Responding Firefighters (Map) -->
                    <section class="mt-6">
                        <h3 class="text-xl font-semibold mb-4">Responding Firefighters</h3>
                        <!-- Google Map -->
                        <!-- Leaflet.js Map Container -->
                        <div id="map"
                            class="bg-gray-200 h-110 w-130 rounded shadow flex items-center justify-center">
                            <!-- Map will be rendered here -->
                        </div>
                    </section>
                </div>
            </main>
    </div>
    </div>
</x-dashboard>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initMap(); // Ensure this is correctly triggered after the content loads
    });

    let selectedTeams = []; // Array to store selected team IDs
    let map;
    // let bounds = L.latLngBounds([fireStationLocation]);

    // Function to toggle team selection (visual state)
    function toggleTeamSelection(teamId) {
        const teamButton = document.querySelector(`button[data-team-id="${teamId}"]`);

        // Check if the team ID is already selected
        if (selectedTeams.includes(teamId)) {
            // If already selected, remove the team ID from the array
            selectedTeams = selectedTeams.filter(id => id !== teamId);
            teamButton.classList.remove('bg-blue-700'); // Deselect by changing color
        } else {
            // If not selected, add the team ID to the array
            selectedTeams.push(teamId);
            teamButton.classList.add('bg-blue-700'); // Select by changing color
        }

        // Update the hidden input with the selected teams
        document.getElementById('selected_teams').value = selectedTeams.join(',');

        console.log('Selected Teams:', selectedTeams); // Check the console for debugging
    }

    // Function to handle form submission (for Mark as Contained)
    function markAsContained(event, form) {
        event.preventDefault(); // Prevent the default form submission

        // Send the form data via AJAX (POST method)
        let formData = new FormData(form);

        fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Show a success dialog box
                    alert(data.message); // Or use a custom modal for better UI

                    // Reload the page to update the status
                    location.reload();
                } else {
                    // Show error message
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong. Please try again later.');
            });
    }

    // Initialize the map
    function initMap() {
        // Set the initial center of the map (fire station location for example)
        const fireStationLocation = [{{ $fireStation->latitude }}, {{ $fireStation->longitude }}];

        map = L.map('map').setView(fireStationLocation, 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Place a marker for the fire station itself
        L.marker(fireStationLocation)
            .addTo(map)
            .bindPopup('Fire Station Location');

        // Now loop through the incidents and place markers for each
        const incidents = @json($onResponseFireReports); // Get the incidents from PHP

        incidents.forEach(incident => {
            if (incident.latitude && incident.longitude) {
                const incidentCoords = [incident.latitude, incident.longitude];

                // Create a marker for each incident
                L.marker(incidentCoords)
                    .addTo(map)
                    .bindPopup(
                        `<strong>${incident.description}</strong><br>Status: ${incident.status}<br>Incident ID: ${incident.id}`
                    );
            }
        });
    }
</script>
