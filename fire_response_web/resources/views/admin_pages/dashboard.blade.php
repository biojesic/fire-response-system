<x-dashboard>
    <div class="flex min-h-screen">
        <main class="flex-1 p-6 ml-5">
            <!-- Parent Container for 3 Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Active Incidents -->
                <section class="mt-6">
                    <h3 class="text-xl font-semibold mb-4">Active Incidents</h3>
                    @if ($activeFireReports->isEmpty())
                        <p>No active incidents at the moment.</p>
                    @else
                        <!-- Scrollable Parent Container for Active Incidents -->
                        <div class="bg-white p-4 rounded shadow h-96 overflow-y-auto">
                            @foreach ($activeFireReports as $incident)
                                <div class="bg-gray-100 p-4 rounded mb-4">
                                    <h4 class="font-semibold">{{ $incident->description }} - Status:
                                        {{ $incident->status }}</h4>
                                    <p>Location: {{ $incident->location }}</p>
                                    <form action="{{ route('admin.dispatch', $incident->id) }}" method="POST">
                                        @csrf
                                        <!-- Assign Teams -->
                                        <div class="mt-4">
                                            <label for="teams" class="font-semibold">Assign Teams to this
                                                Incident:</label>
                                            <div class="space-x-2 mt-2">
                                                @foreach ($teams as $team)
                                                    <button type="button"
                                                        class="team-button bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
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
                                    <button class="bg-blue-500 text-white p-2 rounded mt-2 hover:bg-blue-600">
                                        Select
                                    </button>
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
                    <!-- Placeholder for Map -->
                    <div class="bg-gray-200 h-96 rounded shadow flex items-center justify-center">
                        <span class="text-gray-500">Map Placeholder</span>
                    </div> <!-- Map container placeholder -->
                </section>
            </div>
        </main>
    </div>
</x-dashboard>

<script>
    let selectedTeams = []; // Array to store selected team IDs

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

        console.log(selectedTeams);
    }
</script>
