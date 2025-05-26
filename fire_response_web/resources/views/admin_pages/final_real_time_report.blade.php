<x-dashboard>
    @section('title', 'Fire Report Details')
    <div class="container pt-6 ml-21">
        <header class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Final Report Details</h1>
            </div>
        </header>

        {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-800">
            <div><strong>Fire Station:</strong> Placeholder Fire Station</div>
            <div><strong>Location:</strong> Placeholder Location</div>
            <div><strong>Time & Date:</strong> 2025-01-01 10:00:00</div>
            <div><strong>Fire Out:</strong> 10:45:00</div>
            <div><strong>Incident Type:</strong> Structural Fire</div>
            <div><strong>Involved:</strong> Residential Building</div>
            <div><strong>Name of Owner:</strong> Juan Dela Cruz</div>
            <div><strong>Alarm Status:</strong> General Alarm</div>
            <div><strong>Estimated Damage:</strong> ₱1,000,000</div>
            <div><strong>Fatalities:</strong> 0</div>
            <div><strong>Injured:</strong> 2</div>
            <div><strong>Time of Arrival:</strong> 10:05:00</div>
            <div><strong>Number of Houses/Establishments:</strong> 5</div>
            <div><strong>Number of Families Affected:</strong> 10</div>
            <div><strong>Firetrucks Deployed:</strong> 3</div>
            <div><strong>Ground Commander:</strong> Capt. Pedro Santos</div>
            <div><strong>Responding Teams:</strong> Alpha Team, Bravo Team</div>
        </div> --}}

        <div class="bg-white shadow-md rounded-lg p-6">
            <p><strong>Fire Station:</strong> {{ $firestation_name }}</p>
            <p><strong>Incident Type:</strong> {{ $incident_type }}</p>
            <p><strong>Location:</strong> {{ $location }}</p>
            <p><strong>Owner:</strong> {{ $name_of_owner }}</p>
            <p><strong>Alarm Status:</strong> {{ $alarm_status }}</p>
            <p><strong>Damage Estimate:</strong> ₱{{ number_format($estimated_damage) }}</p>
            <p><strong>Fatality:</strong> {{ $fatality }}</p>
            <p><strong>Injured:</strong> {{ $injured }}</p>
            <p><strong>Commander:</strong> {{ $ground_commander }}</p>
        </div>
        {{ dd($firestation_name) }}
    </div>
</x-dashboard>
