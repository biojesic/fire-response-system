<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Firefighter;
use App\Http\Controllers\Log;

class TeamWebController extends Controller
{
        public function index() {
        $fireStationId = auth()->user()->firefighter->fireStationId;

        // Load teams na naka-link sa fireStationId na 'yon
        $teams = Team::with(['firefighters.user', 'firefighters.position'])
            ->where('fireStationId', $fireStationId)
            ->get();

        // Load unassigned firefighters na belong lang din sa same fire station
        $availableFirefighters = Firefighter::with('user', 'position')
            ->where('fireStationId', $fireStationId)
            ->whereNull('teamId')
            ->get();
            
            // dd($teams, $availableFirefighters, $fireStationId);

        return view('admin_pages.teams', compact('teams', 'availableFirefighters'));
    }

    public function assignFirefighter(Request $request, $teamId) {
        try {
            // Find the team by its ID
            $team = Team::findOrFail($teamId);

            // Get the firefighter_id from the form
            $firefighterId = $request->firefighter_id;

            // Find the firefighter by their ID
            $firefighter = Firefighter::findOrFail($firefighterId);

            // Check if the firefighter is already assigned to a team
            if ($firefighter->teamId !== null) {
                return redirect()->route('admin.teams')->with('error', 'Firefighter is already assigned to a team.');
            }

            // Assign the firefighter to the team
            $firefighter->teamId = $team->id;
            $firefighter->save();

            // Redirect back with a success message
            return redirect()->route('admin.teams')->with('success', 'Firefighter assigned to the team.');
        } catch (\Exception $e) {
            // Log the error and display a user-friendly message
            \Log::error("Error assigning firefighter: ".$e->getMessage());
            return redirect()->route('admin.teams')->with('error', 'An error occurred while assigning the firefighter.');
        }
    }

    public function removeFirefighter($teamId, $firefighterId) {
        // Find the team by its ID
        $team = Team::findOrFail($teamId);

        // Find the firefighter by its ID
        $firefighter = Firefighter::findOrFail($firefighterId);

        // Remove the firefighter from the team
        $firefighter->teamId = null; // Unassign the firefighter from the team
        $firefighter->save();

        // Optionally, you can add a success message to the session or return a response
        return redirect()->route('admin.teams')->with('success', 'Firefighter removed from the team.');
    }

}
