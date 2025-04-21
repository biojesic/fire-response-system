<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FireReports;
use App\Models\Team;

class AdminDashboardController extends Controller 
{
    public function showDashboard()
    {
        // Get the fire station of the logged-in firefighter
        $firestationId = auth()->user()->firefighter->fireStationId;
    
        // Get all active FireReports (Pending incidents) for this fire station
        $activeFireReports = FireReports::where('status', 'Pending')
                                        ->where('fireStationId', $firestationId)  // Filter incidents based on fire station
                                        ->get();
    
        // Get all standby teams for this fire station (on-duty teams with 'Standby' status)
        // Exclude teams that already have an assigned fire incident
        $teams = Team::with(['firefighters' => function ($query) {
            $query->where('status', 'Standby');  // Only get on-duty firefighters
        }])
        ->where('fireStationId', $firestationId)  // Filter teams based on fire station
        ->whereNull('assignedFireIncident')      // Exclude teams that already have an assigned incident
        ->get();
    
        // Get new reports (created in the last 24 hours)
        $newReports = FireReports::where('created_at', '>=', now()->subDay())
                                 ->where('fireStationId', $firestationId)  // Filter new reports based on fire station
                                 ->get();
    
        // Return data to view (fire reports, teams, new reports)
        return view('admin_pages.dashboard', compact('activeFireReports', 'teams', 'newReports'));
    }
    


    public function showRespondingFirefighters($incidentId) {
    $incident = Incident::find($incidentId);

    // Get all firefighters assigned to the active incident
    $respondingFirefighters = $incident->firefighters;  // Assuming relationship

    // Fetch firefighter locations (latitude, longitude)
    $firefighterLocations = [];
    foreach ($respondingFirefighters as $firefighter) {
        $location = $firefighter->locations()->latest()->first();  // Get latest location
        if ($location) {
            $firefighterLocations[] = [
                'name' => $firefighter->name,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude
            ];
        }
    }

    return view('admin.responding-firefighters-map', compact('firefighterLocations'));
    }

//     public function assignTeamToIncident(Request $request, $incidentId)
// {
//     // Get the fire station of the logged-in firefighter
//     $firestationId = auth()->user()->firefighter->fireStationId;

//     // Find the incident
//     $incident = FireReports::findOrFail($incidentId);

//     // Check if the incident is still pending
//     if ($incident->status != 'Pending') {
//         return redirect()->route('admin.dashboard')->with('error', 'Incident is no longer pending.');
//     }

//     // Get the first standby team for this fire station
//     $team = Team::where('status', 'Standby')
//                 ->where('fireStationId', $firestationId) // Filter teams based on fire station
//                 ->first();

//     if ($team) {
//         // Assign the team to the incident by updating the team's assignedFireIncident
//         $team->assignedFireIncident = $incident->id; // Assigning the incident's ID to the team's assignedFireIncident
//         $team->status = 'On Response'; // Update team status
//         $team->save();

//         // Update the incident status
//         $incident->status = 'Responding'; // Mark the incident as being handled
//         $incident->save();

//         return redirect()->route('admin.dashboard')->with('message', 'Team dispatched to the incident successfully!');
//     }

//     return redirect()->route('admin.dashboard')->with('error', 'No standby teams available.');
// }

public function assignTeamToPendingIncident(Request $request, $teamId)
{
    // Get the fire station of the logged-in firefighter
    $firestationId = auth()->user()->firefighter->fireStationId;

    // Find the team
    $team = Team::where('id', $teamId)
                ->where('fireStationId', $firestationId) // Filter teams based on fire station
                ->firstOrFail();

    // Get the first pending incident for this fire station
    $incident = FireReports::where('status', 'Pending')
                            ->where('fireStationId', $firestationId)  // Filter incidents based on fire station
                            ->first();

    if ($incident) {
        // Assign the team to the incident
        $incident->assignedFireIncident = $incident->id;
        $incident->status = 'Responding'; // Update incident status
        $incident->save();

        // Update the team's status
        $team->status = 'On Response';
        $team->save();

        return redirect()->route('admin.dashboard')->with('message', 'Team dispatched to the incident successfully!');
    }

    return redirect()->route('admin.dashboard')->with('error', 'No pending incidents found.');
}

public function dispatchToIncident(Request $request, $incidentId)
{
    // Get the fire station of the logged-in firefighter
    $firestationId = auth()->user()->firefighter->fireStationId;

    // Find the incident
    $incident = FireReports::findOrFail($incidentId);

    // Check if the incident is still pending
    if ($incident->status != 'Pending') {
        return redirect()->route('admin.dashboard')->with('error', 'Incident is no longer pending.');
    }

    // Get the selected teams from the request
    $selectedTeamIds = explode(',', $request->input('selected_teams', ''));

    // Check if any teams were selected
    if (empty($selectedTeamIds)) {
        return redirect()->route('admin.dashboard')->with('error', 'No teams selected.');
    }

    // Dispatch each selected team to the incident
    foreach ($selectedTeamIds as $teamId) {
        $team = Team::where('id', $teamId)
                    ->where('fireStationId', $firestationId)  // Ensure the team is from the same fire station
                    ->first();

        if ($team) {
            // Assign the team to the incident
            $team->assignedFireIncident = $incident->id;
            $team->status = 'On Response'; // Update team status
            $team->save();

            // Update the incident status
            $incident->status = 'Responding';
            $incident->save();
        }
    }

    return redirect()->route('admin.dashboard')->with('message', 'Selected teams dispatched to the incident successfully!');
}


    
}
