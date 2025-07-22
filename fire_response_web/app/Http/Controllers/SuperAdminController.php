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
use App\Models\PersonalEquipment;
// use App\Controllers\FireReportsController;
// use App\Controllers\PersonalEquipmentController;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

// use App\Models\Equipment;
// use App\Models\Alert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    public function fireResponseDashboard()
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

        return view('superadmin_pages.superadmin_fire_response', [
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

public function dashboard()
{
    // Get authenticated user and their position
    $user = Auth::user();
    $position = $user->firefighter->position;

    // Incident statistics
    $totalIncidents = FireReports::count();
    $monthlyIncidents = FireReports::where('created_at', '>=', Carbon::now()->subDays(30))->count();
    $incidentChange = $this->calculatePercentageChange(
        FireReports::whereBetween('created_at', [Carbon::now()->subDays(60), Carbon::now()->subDays(30)])->count(),
        $monthlyIncidents
    );

    // Responder statistics
    $activeResponders = Firefighter::whereIn('status', ['StandBy', 'On Response'])->count();
    $lastWeekResponders = Firefighter::where('status', 'StandBy')
        ->where('updated_at', '>=', Carbon::now()->subDays(7))
        ->count();
    $responderChange = $this->calculatePercentageChange(
        Firefighter::where('status', ['StandBy', 'On Response'])
            ->whereBetween('updated_at', [Carbon::now()->subDays(14), Carbon::now()->subDays(7)])
            ->count(),
        $lastWeekResponders
    );

    // Equipment statistics
    $totalEquipment = PersonalEquipment::sum('quantities');
    $lastWeekEquipment = PersonalEquipment::where('updated_at', '>=', now()->subDays(7))
        ->sum('quantities');
    $equipmentChange = $this->calculatePercentageChange(
        PersonalEquipment::whereBetween('updated_at', [now()->subDays(14), now()->subDays(7)])
            ->sum('quantities'),
        $lastWeekEquipment
    );

    // Response time statistics
    $avgResponseTime = FireReports::whereNotNull('response_time')
        ->avg('response_time');
    $lastMonthResponse = FireReports::whereNotNull('response_time')
        ->where('created_at', '>=', Carbon::now()->subDays(30))
        ->avg('response_time');
    $responseTimeChange = $this->calculatePercentageChange(
        FireReports::whereNotNull('response_time')
            ->whereBetween('created_at', [Carbon::now()->subDays(60), Carbon::now()->subDays(30)])
            ->avg('response_time'),
        $lastMonthResponse,
        true // For response time, lower is better
    );

    // User statistics with consistent civilian filter
    $activeUsers = User::where('userStatus', 'Active')
                     ->where('userRole', 'civilian')
                     ->count();
    
    $activeUsersChange = $this->calculatePercentageChange(
        User::where('userStatus', 'Active')
            ->where('userRole', 'civilian')
            ->whereBetween('created_at', [Carbon::now()->subDays(60), Carbon::now()->subDays(30)])
            ->count(),
        User::where('userStatus', 'Active')
            ->where('userRole', 'civilian')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count()
    );

    $unverifiedUsers = User::where('userStatus', 'Unverified')
                         ->where('userRole', 'civilian')
                         ->count();
    
    $newUnverifiedUsers = User::where('userStatus', 'Unverified')
                            ->where('userRole', 'civilian')
                            ->where('created_at', '>=', Carbon::now()->subDays(7))
                            ->count();

    $inactiveUsers = User::where('userStatus', 'Inactive')
                       ->where('userRole', 'civilian')
                       ->count();
    
    $rejectedUsers = User::where('userStatus', 'Rejected')
                       ->where('userRole', 'civilian')
                       ->count();

    // Monthly growth data for civilians
    $monthlyGrowth = User::where('userRole', 'civilian')
        ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

    // Verification trends data (last 4 weeks)
    $verificationTrends = [
        'verified' => [
            User::where('userStatus', 'Active')
                ->where('userRole', 'civilian')
                ->whereBetween('created_at', [now()->subDays(7), now()])
                ->count(),
            User::where('userStatus', 'Active')
                ->where('userRole', 'civilian')
                ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
                ->count(),
            User::where('userStatus', 'Active')
                ->where('userRole', 'civilian')
                ->whereBetween('created_at', [now()->subDays(21), now()->subDays(14)])
                ->count(),
            User::where('userStatus', 'Active')
                ->where('userRole', 'civilian')
                ->whereBetween('created_at', [now()->subDays(28), now()->subDays(21)])
                ->count(),
        ],
        'rejected' => [
            User::where('userStatus', 'Rejected')
                ->where('userRole', 'civilian')
                ->whereBetween('created_at', [now()->subDays(7), now()])
                ->count(),
            User::where('userStatus', 'Rejected')
                ->where('userRole', 'civilian')
                ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
                ->count(),
            User::where('userStatus', 'Rejected')
                ->where('userRole', 'civilian')
                ->whereBetween('created_at', [now()->subDays(21), now()->subDays(14)])
                ->count(),
            User::where('userStatus', 'Rejected')
                ->where('userRole', 'civilian')
                ->whereBetween('created_at', [now()->subDays(28), now()->subDays(21)])
                ->count(),
        ]
    ];

    // Recent incidents for map
    $recentIncidents = FireReports::with('barangay')
        ->orderBy('created_at', 'desc')
        ->take(20)
        ->get();

    // Team status
    $teams = Team::withCount('firefighters')
        ->with(['firefighters' => function($query) {
            $query->where('status', 'StandBy');
        }])
        ->get();

    // Incident trends data (last 12 months)
    $incidentTrends = FireReports::selectRaw('
            YEAR(created_at) as year, 
            MONTH(created_at) as month, 
            COUNT(*) as count')
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

    return view('superadmin_pages.superadmin_dashboard', [
        'user' => $user,
        'position' => $position,
        'stats' => [
            'total_incidents' => $totalIncidents,
            'incident_change' => $incidentChange,
            'active_responders' => $activeResponders,
            'responder_change' => $responderChange,
            'available_equipment' => $totalEquipment,
            'equipment_change' => $equipmentChange,
            'avg_response_time' => round($avgResponseTime, 1),
            'response_time_change' => $responseTimeChange,
            'active_users' => $activeUsers,
            'active_users_change' => $activeUsersChange,
            'unverified_users' => $unverifiedUsers,
            'new_unverified_users' => $newUnverifiedUsers,
            'inactive_users' => $inactiveUsers,
            'rejected_users' => $rejectedUsers,
        ],
        'recentIncidents' => $recentIncidents,
        'teams' => $teams,
        'incidentTrends' => $incidentTrends,
        'monthly_growth' => $monthlyGrowth,
        'verification_trends' => $verificationTrends
    ]);
}

    /**
     * Calculate percentage change between two values
     * 
     * @param float $oldValue The older value
     * @param float $newValue The newer value
     * @param bool $inverted Whether improvement means lower value (like response time)
     * @return array [value => rounded %, improved => boolean]
     */
    private function calculatePercentageChange($oldValue, $newValue, $inverted = false)
    {
        if ($oldValue == 0) {
            return [
                'value' => 0,
                'improved' => false
            ];
        }

        $change = (($newValue - $oldValue) / $oldValue) * 100;
        $rounded = round($change, 1);

        if ($inverted) {
            return [
                'value' => abs($rounded),
                'improved' => $change < 0
            ];
        }

        return [
            'value' => abs($rounded),
            'improved' => $change > 0
        ];
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
