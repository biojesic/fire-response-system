<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FireReports;
use App\Models\AssignedIncident;
use App\Models\Firefighter;

class AssignedIncidentController extends Controller
{
    public function showAssignedIncidentByFirefighter($firefighterId)
    {
        // Fetch the firefighter from the database (assuming you have a Firefighter model)
        $firefighter = Firefighter::findOrFail($firefighterId);

        // Get the assigned incident for the firefighter
        // Assuming the Firefighter model has a relation to AssignedIncidents
        $incident = FireReports::with('assignedTeams.firefighters.position')
                               ->whereHas('assignedTeams', function ($query) use ($firefighter) {
                                    // Ensure the firefighter is in the assigned teams
                                    $query->whereHas('firefighters', function ($query) use ($firefighter) {
                                        $query->where('firefighters.id', $firefighter->id);
                                    });
                                })
                               ->firstOrFail(); // Get the first matching incident

        // Check if there are assigned teams
        if ($incident->assignedTeams->isEmpty()) {
            return response()->json(['error' => 'No teams assigned to this incident'], 404);
        }

        // Create an array to hold all assigned teams' details
        $assignedIncidentDetails = [];

        // Loop through all assigned teams
        foreach ($incident->assignedTeams as $assignedTeam) {
            // Get the team leader for each team
            $teamLeader = $assignedTeam->firefighters->firstWhere(function ($firefighter) {
                return $firefighter->position->position_name == 'Team Leader';  // Adjust based on your position_name field
            });

            // Add the team details to the response
            $assignedIncidentDetails[] = [
                'firefighter_id' => $firefighter->id,
                'teamName' => $assignedTeam->teamName,
                'teamLeader' => $teamLeader ? $teamLeader->user->userFirstName . ' ' . $teamLeader->user->userLastName : 'N/A',
                'location' => $incident->location,
                'landmark' => $incident->landmark,
                'timeReported' => $incident->created_at->format('h:i A'),
                'latitude' => $incident->latitude,
                'longitude' => $incident->longitude,
                'fireReportId' => $incident->id
            ];
        }

        // Return the assigned incident details specific to the logged-in firefighter
        return response()->json($assignedIncidentDetails);
    }
}
