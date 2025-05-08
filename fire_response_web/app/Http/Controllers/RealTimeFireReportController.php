<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FireReports;
use App\Models\RealTimeFireReport;
use App\Models\Firefighter;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RealTimeFireReportController extends Controller {
    // Show all real-time fire reports (for mobile)
    public function index($fireReportId)
    {
        $fireReports = RealTimeFireReport::where('fire_report_id', $fireReportId)->get();
        return response()->json([
            'success' => true,
            'data' => $fireReports,
        ], 200);
    }

    // Create a new real-time fire report (form to create report via API)
    public function create($fireReportId)
    {
        // You can handle validation and data logic here if necessary
        return response()->json([
            'success' => true,
            'message' => 'Ready to create a real-time fire report.',
        ], 200);
    }

    // Store a new real-time fire report
    public function store(Request $request, $fireReportId)
    {
        $validated = $request->validate([
            'stage' => 'required|string',
            'content' => 'required|array',
        ]);

        $realTimeFireReport = RealTimeFireReport::create([
            'fire_report_id' => $fireReportId,
            'stage' => $validated['stage'],
            'content' => $validated['content'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Real-time fire report created successfully.',
            'data' => $realTimeFireReport
        ], 201);
    }

    // Show a specific real-time fire report (for mobile)
    public function show($id)
    {
        $realTimeFireReport = RealTimeFireReport::find($id);

        if (!$realTimeFireReport) {
            return response()->json([
                'success' => false,
                'message' => 'Report not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $realTimeFireReport
        ], 200);
    }

    // Edit a real-time fire report (optional)
    public function edit($id)
    {
        $realTimeFireReport = RealTimeFireReport::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $realTimeFireReport
        ], 200);
    }

    // Update a real-time fire report (optional)
    public function update(Request $request, $id)
    {
        $realTimeFireReport = RealTimeFireReport::findOrFail($id);

        $validated = $request->validate([
            'stage' => 'required|string',
            'content' => 'required|array',
        ]);

        $realTimeFireReport->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Real-time fire report updated successfully.',
            'data' => $realTimeFireReport
        ], 200);
    }

    // Delete a real-time fire report
    public function destroy($id)
    {
        $realTimeFireReport = RealTimeFireReport::findOrFail($id);
        $realTimeFireReport->delete();

        return response()->json([
            'success' => true,
            'message' => 'Real-time fire report deleted successfully.'
        ], 200);
    }

    // Get initial fire report information (for mobile)
    public function showInitial($id)
    {
        $report = FireReports::findOrFail($id);
        $user = auth()->user();
        $firestationName = $user->firefighter->firestation->firestationName;
        $firestationContactNum = $user->firefighter->firestation->firestationContactNumber;
        $location = $report->location;
        $timeAndDateReported = $report->created_at;
        $senderFirstName = $user->userFirstName;
        $senderLastName = $user->userLastName;
        $userRank = $user->firefighter->rank->rank_name ? $user->firefighter->rank->rank_name : 'No Rank';
        $senderInfo = $userRank . ' ' . $senderFirstName . ' ' . $senderLastName . ' - ' . $firestationContactNum;

        return response()->json([
            'success' => true,
            'data' => [
                'firestationName' => $firestationName,
                'location' => $location,
                'timeAndDateReported' => $timeAndDateReported,
                'senderInfo' => $senderInfo,
            ]
        ], 200);
    }

    // Create initial report (for mobile)
    public function createInitial($id, Request $request)
    {
        $request->validate([
            'firestation_name' => 'required|string|max:255',
            'incident_type' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'responding_team' => 'required|string|max:255',
            'time_and_date' => 'required|date',
            'involved' => 'required|string|max:255',
            'time_of_arrival' => 'required|string|max:255',
            'fire_out' => 'nullable|string|max:255',
            'ground_commander' => 'required|string|max:255',
        ]);

        $content = [
            'firestation_name' => $request->firestation_name,
            'incident_type' => $request->incident_type,
            'location' => $request->location,
            'responding_team' => $request->responding_team,
            'time_and_date' => $request->time_and_date,
            'involved' => $request->involved,
            'time_of_arrival' => $request->time_of_arrival,
            'fire_out' => $request->fire_out,
            'ground_commander' => $request->ground_commander,
        ];

        $fireReport = new RealTimeFireReport();
        $fireReport->fire_report_id = $id;
        $fireReport->stage = 'initial';
        $fireReport->content = json_encode($content);
        $fireReport->user_id = $userId;
        $fireReport->save();

        return response()->json([
            'success' => true,
            'message' => 'Fire report has been submitted successfully.',
        ], 201);
    }
    
    // Progressive report creation (for mobile)
    public function createProgress($id)
    {
        $report = FireReports::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $report
        ], 200);
    }

    public function createFinal($id, Request $request) {
    // Validate the incoming request data
    $request->validate([
        'incident_type' => 'required|string|max:255',
        'involved' => 'required|string|max:255',
        'name_of_owner' => 'required|string|max:255',
        'alarm_status' => 'required|string|max:255',
        'time_of_arrival' => 'required|date_format:H:i:s',
        'estimated_damage' => 'required|numeric|max:9999999999',
        'fatality' => 'required|string|max:255',
        'injured' => 'required|string|max:255',
        'number_of_houses_establishments' => 'required|numeric|max:9999999999',
        'number_of_families_affected' => 'required|numeric|max:9999999999',
        'ground_commander' => 'required|string|max:255',
        'number_of_firetrucks' => 'required|numeric|max:9999999999',
    ]);

    // Retrieve the fire report and logged-in user
    $fireReport = FireReports::findOrFail($id);
    $user = auth()->user();

    // Auto-fill the necessary fields
    $firestationName = $user->firefighter->firestation->firestationName;
    $location = $fireReport->location;
    $timeAndDate = $fireReport->created_at;
    $fireOutTime = $fireReport->marked_as_contained_at
    ? $fireReport->marked_as_contained_at->format('H:i:s')
    : 'N/A';

    // Get assigned teams
    $respondingTeam = $fireReport->assignedTeams->pluck('teamName')->toArray();

    // Prepare the content to be stored
    $content = [
        'firestation_name' => $firestationName,
        'incident_type' => $request->incident_type,
        'involved' => $request->involved,
        'name_of_owner' => $request->name_of_owner,
        'alarm_status' => $request->alarm_status,
        'estimated_damage' => $request->estimated_damage,
        'fatality' => $request->fatality,
        'location' => $location,
        'responding_team' => $respondingTeam,
        'time_and_date' => $timeAndDate,
        'injured' => $request->injured,
        'time_of_arrival' => $request->time_of_arrival,
        'number_of_houses_establishments' => $request->number_of_houses_establishments,
        'number_of_families_affected' => $request->number_of_families_affected,
        'number_of_firetrucks' => $request->number_of_firetrucks,
        'fire_out' => $fireOutTime,
        'ground_commander' => $request->ground_commander,
    ];

    // Create a new RealTimeFireReport and assign data
    $fireReportFinal = new RealTimeFireReport();
    $fireReportFinal->fire_report_id = $id;
    $fireReportFinal->stage = 'final';
    $fireReportFinal->content = json_encode($content);
    $fireReportFinal->user_id = $user->id;
    $fireReportFinal->save();

    return response()->json([
        'success' => true,
        'message' => 'Fire report (final stage) has been submitted successfully.',
        'submitted_data' => $content,
        'fire_report_id' => $fireReportFinal->fire_report_id,
        'fire_report_stage' => $fireReportFinal->stage,
        'user_id' => $user->id,
    ], 201);
}

    
    public function markAsContained($fireReportId)
{
    // Retrieve the fire report and logged-in user
    $fireReport = FireReports::findOrFail($fireReportId);
    $user = auth()->user();

    // Check if the user is a firefighter
    $firefighter = Firefighter::where('userId', $user->id)->first();

    if (!$firefighter) {
        return response()->json([
            'message' => 'No firefighter found for this user.',
        ], 404);
    }

    // Find all teams assigned to this fire report
    $teams = Team::where('assignedFireIncident', $fireReport->id)->get();

    // Check if the firefighter is part of any of the teams or is a dispatcher
    if ($firefighter->position->position_name === 'Radio Operator') {
        $authorized = true;
    } else {
        // Allow any Radio Operator to mark the fire as contained,
        // or any firefighter who is part of the assigned team.
        $authorized = $teams->contains(function ($team) use ($firefighter) {
            return $team->firefighters->contains(function ($member) use ($firefighter) {
                return $member->id === $firefighter->id;
            });
        });
    }

    // If the firefighter is not authorized, return unauthorized
    if (!$authorized) {
        return response()->json([
            'message' => 'You are not authorized to mark this fire report as contained.',
        ], 403);
    }

    // Update the fire report status to "Resolved"
    $fireReport->status = 'Fire Out';
    $fireReport->marked_as_contained_by_id = $firefighter->id;
    $fireReport->marked_as_contained_at = now();
    $fireReport->save();

    // Update the status and assignedFireIncident of all teams
    foreach ($teams as $team) {
        $team->status = 'Standby';
        $team->assignedFireIncident = null;
        $team->save();
    }

    // return response()->json([
    //     'success' => true,
    //     'message' => 'Fire report has been marked as contained successfully.',
    // ], 200);
    return response()->json([
        'firefighter_id' => $firefighter->id,
        'user' => $user,
        'fireReport' => $fireReport,
    ]);
    
}


}
