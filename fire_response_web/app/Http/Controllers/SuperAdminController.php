<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FireReports;
use App\Models\Team;
use App\Models\FireStation;
use App\Models\Firefighter;
use App\Models\FirefighterRank;
use App\Models\RealTimeFireReport;
use App\Models\FireFighterReports;
use App\Controllers\FireReportsController;
use App\Models\User;

class SuperAdminController extends Controller
{
    public function showSuperAdminDashboard()
    {
        $user = auth()->user();

        if (!$user) {
        return redirect()->route('login')->with('error', 'Please login first');
    }

        // Then check if user has firefighter record
    if (!$user->firefighter) {
        auth()->logout(); // Log out the user since they're invalid
        return redirect()->route('login')->with('error', 'Your account is not properly set up as a firefighter');
    }

        $firestationId = auth()->user()->firefighter->fireStationId;

        $fireStation = FireStation::find($firestationId);

        $fireReports = FireReports::all();

        $position = $user->firefighter->position;

        $pendingFireReports = FireReports::where('status', 'Pending')
            ->get();

        $onResponseFireReports = FireReports::where('status', 'Responding')
            ->get();

        // Check if the initial report has been submitted for each report in `onResponseFireReports`
        foreach ($onResponseFireReports as $onResponse) {
            $realTimeReport = RealTimeFireReport::where('fire_report_id', $onResponse->id)->first();
            $onResponse->stage = $realTimeReport ? $realTimeReport->stage : null;
        }

        $onResponseFirefighters = DB::table('firefighters')
            ->join('locations', 'firefighters.userId', '=', 'locations.user_id') // join on user_id
            ->join('teams', 'firefighters.teamId', '=', 'teams.id')
            ->where('firefighters.status', 'On Response')
            ->where('firefighters.fireStationId', $firestationId)
            ->get(['firefighters.*', 'locations.latitude', 'locations.longitude']);


        $teams = Team::with(['firefighters' => function ($query) {
            $query->where('status', 'Standby');
        }])
            ->whereNull('assignedFireIncident')
            ->get();

        $newReports = FireReports::where('created_at', '>=', now()->subDay())
            ->get();

        // Retrieve Fire Reports with False Alarm status
        $falseAlarmFireReports = FireReports::where('status', 'False Alarm')
            ->get();

        return view('superadmin_pages.superadmin_dashboard', [
            'user' => $user,
            'position' => $position,
            'pendingFireReports' => $pendingFireReports,
            'onResponseFireReports' => $onResponseFireReports,
            'teams' => $teams,
            'newReports' => $newReports,
            'fireStation' => $fireStation,
            'fireReports' => $fireReports,
            'onResponseFirefighters' => $onResponseFirefighters,
            'falseAlarmFireReports' => $falseAlarmFireReports,
        ]);
    }

    public function superAdmindispatchToIncident(Request $request, $incidentId)
    {
        // Get the fire station of the logged-in firefighter
        // $firestationId = auth()->user()->firefighter->fireStationId;

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
                // ->where('fireStationId', $firestationId)
                ->first();

            if ($team) {
                // Assign the team to the incident
                $team->assignedFireIncident = $incident->id;
                $team->status = 'On Response'; // Update team status
                $team->save();

                // Log firefighters (only not Off Duty) to firefighter_reports
                $firefighters = $team->firefighters()
                    ->where('status', '!=', 'Off Duty')
                    ->get();

                foreach ($firefighters as $firefighter) {
                    FirefighterReports::firstOrCreate([
                        'fireReportId' => $incident->id,
                        'fireFighterId' => $firefighter->id,
                    ]);
                }

                // Update the incident status
                $incident->status = 'Responding';
                // dd($incident);
                $incident->save();
            }
        }

        return redirect()->route('superadmin.dashboard')->with('message', 'Selected teams dispatched to the incident successfully!');
    }
}
