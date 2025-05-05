<x-dashboard>
    @section('title', 'Fire Emergency - BFP')
    <div class="container w-270 pt-6 ml-20">
        {{-- <main class="flex-1 p-6 pt-0">
            <main class="flex-1 p-6 ml-10 "> --}}

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
            <section>
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
                                            <form action="{{ route('admin.dispatch', $incident->id) }}" method="POST">
                                                @csrf
                                                <!-- Assign Teams -->
                                                <div class="mt-4">
                                                    <label for="teams" class="font-semibold">Assign Teams to
                                                        this Incident:</label>
                                                    <div class="space-x-2 mt-2">
                                                        @foreach ($teams as $team)
                                                            <button type="button"
                                                                class="team-button bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-2"
                                                                data-incident-id="{{ $incident->id }}"
                                                                data-team-id="{{ $team->id }}"
                                                                onclick="toggleTeamSelection({{ $incident->id }}, {{ $team->id }})">
                                                                {{ explode(' -', $team->teamName)[0] }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Hidden input to store selected team IDs -->
                                                <input type="hidden" name="selected_teams"
                                                    id="selected_teams_{{ $incident->id }}">

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
                                            <div class="mt-4 flex flex-wrap gap-2">
                                                @if ($onResponse->stage !== 'initial')
                                                    <!-- Submit Initial Report -->
                                                    <a href="{{ route('fire-reports.initial.show', $onResponse->id) }}"
                                                        class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                                                        Submit Initial Report
                                                    </a>
                                                    {{-- @else
                                                    <!-- Button is hidden if the stage is 'initial' -->
                                                    <span class="text-gray-400">Initial Report Submitted</span> --}}
                                                @endif

                                                <!-- Submit Progress Report -->
                                                <a href="{{ route('fire-reports.progressive.create', $onResponse->id) }}"
                                                    class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                                                    Submit Progress Report
                                                </a>

                                                <!-- Submit Final Report -->
                                                <a href="{{ route('fire-reports.final.create', $onResponse->id) }}"
                                                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                                    Submit Final Report
                                                </a>
                                            </div>


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
                <section>
                    <h3 class="text-xl font-semibold mb-4">Standby Teams</h3>
                    <div class="bg-white p-4 rounded shadow h-96 overflow-y-auto">
                        @foreach ($teams as $team)
                            <div class="bg-white p-4 rounded shadow mb-4">
                                <h4 class="font-semibold text-lg">Team {{ explode(' -', $team->teamName)[0] }}
                                </h4>
                                <p>On-Duty Firefighters:</p>
                                <div class="h-20 overflow-y-auto">
                                    <ul>
                                        @if ($team->firefighters->isNotEmpty())
                                            @foreach ($team->firefighters as $firefighter)
                                                <li>{{ $firefighter->user->userLastName }} -
                                                    {{ $firefighter->position->position_name }}</li>
                                            @endforeach
                                        @else
                                            <li>No firefighters assigned yet.</li>
                                        @endif
                                    </ul>
                                </div>
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
            <section>
                <h3 class="text-xl font-semibold mb-4">Responding Firefighters</h3>
                <!-- Leaflet.js Map Container -->
                <div id="map" class="bg-gray-200 h-100 w-110 rounded shadow flex items-center justify-center">
                </div>
                <div class="mt-3">
                    <!-- Legend Title -->
                    {{-- <h3 class="text-xl font-semibold mb-1">Legend</h3> --}}

                    <!-- Grid Layout for Icons and Descriptions -->
                    <div class="grid grid-cols-3 w-100">
                        <!-- Fire Incident Icon with Description -->
                        <div class="flex items-center justify-right">
                            <img src="/images/fire-icon.png" alt="Fire Incident" class="h-7 w-7 mr-1">
                            <p class="text-xs font-semibold">Fire Incident</p>
                        </div>

                        <!-- Fire Incident Icon with Description -->
                        <div class="flex items-center justify-right">
                            <img src="/images/fireresponder-icon.webp" alt="Fire Incident" class="h-9 w-9">
                            <p class="text-xs font-semibold">Fire Responder</p>
                        </div>

                        <!-- Fire Incident Icon with Description -->
                        <div class="flex items-center justify-right">
                            <img src="/images/firestation-icon.png" alt="Fire Incident" class="h-7 w-7 mr-2">
                            <p class="text-xs font-semibold">Fire Station</p>
                        </div>
                    </div>
                </div>

            </section>
        </div>
        {{-- </main>
        </main> --}}
    </div>
</x-dashboard>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initMap(); // Ensure this is correctly triggered after the content loads
    });

    let selectedTeams = []; // Array to store selected team IDs
    let map;

    var firestationIcon = L.icon({
        iconUrl: '/images/firestation-icon.png', // The path to your image
        iconSize: [45, 45], // Size of the icon (width, height)
        iconAnchor: [25, 50], // Point of the icon which will correspond to marker's position
        popupAnchor: [0, -50] // Offset of the popup relative to the icon
    });

    var fireincidentIcon = L.icon({
        iconUrl: '/images/fire-icon.png', // The path to your image
        iconSize: [50, 50], // Size of the icon (width, height)
        iconAnchor: [25, 50], // Point of the icon which will correspond to marker's position
        popupAnchor: [0, -50] // Offset of the popup relative to the icon
    });

    var fireresponderIcon = L.icon({
        iconUrl: '/images/fireresponder-icon.webp', // The path to your image
        iconSize: [55, 55], // Size of the icon (width, height)
        iconAnchor: [25, 50], // Point of the icon which will correspond to marker's position
        popupAnchor: [0, -50] // Offset of the popup relative to the icon
    });


    // Function to toggle team selection (visual state) for each incident
    function toggleTeamSelection(incidentId, teamId) {
        // Get the selected teams for this incident (stored in a hidden input for each incident)
        let selectedTeams = document.getElementById(`selected_teams_${incidentId}`).value.split(',');

        const teamButton = document.querySelector(`button[data-incident-id="${incidentId}"][data-team-id="${teamId}"]`);

        // Check if the team ID is already selected
        if (selectedTeams.includes(teamId.toString())) {
            // If already selected, remove the team ID from the array
            selectedTeams = selectedTeams.filter(id => id !== teamId.toString());
            teamButton.classList.remove('bg-blue-700'); // Deselect by changing color
        } else {
            // If not selected, add the team ID to the array
            selectedTeams.push(teamId.toString());
            teamButton.classList.add('bg-blue-700'); // Select by changing color
        }

        // Update the hidden input with the selected teams for this incident
        document.getElementById(`selected_teams_${incidentId}`).value = selectedTeams.join(',');

        console.log('Selected Teams for Incident ' + incidentId + ':',
            selectedTeams); // Check the console for debugging
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

        const fireStationLocation = [{{ $fireStation->latitude }}, {{ $fireStation->longitude }}];

        map = L.map('map').setView(fireStationLocation, 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Place a marker for the fire station itself
        L.marker(fireStationLocation, {
                icon: firestationIcon
            })
            .addTo(map)
            .bindPopup('Fire Station Location');

        // Now loop through the incidents and place markers for each
        const incidents = @json($onResponseFireReports); // Get the incidents from PHP

        incidents.forEach(incident => {
            if (incident.latitude && incident.longitude) {
                const incidentCoords = [incident.latitude, incident.longitude];

                // Create a marker for each incident
                L.marker(incidentCoords, {
                        icon: fireincidentIcon
                    })
                    .addTo(map)
                    .bindPopup(
                        `<strong>${incident.location}</strong><br>Status: ${incident.status}<br>Incident ID: ${incident.id}`
                    );
            }
        });

        const firefighterLocations = @json($onResponseFirefighters); // Data passed from PHP

        firefighterLocations.forEach(location => {
            const firefighterCoords = [location.latitude, location.longitude];

            // Create a marker for each firefighter
            L.marker(firefighterCoords, {
                    icon: fireresponderIcon
                })
                .addTo(map)
                .bindPopup(
                    `<strong>Firefighter Location</strong><br>
             Latitude: ${location.latitude}<br>
             Longitude: ${location.longitude}`
                );
        });

    }
</script>
