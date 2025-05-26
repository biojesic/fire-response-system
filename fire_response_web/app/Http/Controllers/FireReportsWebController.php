<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FireReports;
use App\Models\FireStation;
use App\Models\Team;
use App\Models\User;
use App\Models\Firefighter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class FireReportsWebController extends Controller
{ 
    public function index(Request $request)
    {
        $admin = auth()->user();
        $adminFirefighter = $admin->firefighter;
    
        if (!$adminFirefighter || !$adminFirefighter->fireStationId) {
            return redirect()->route('admin.dashboard')->withErrors('No fire station assigned.');
        }
    
        // Fetch reports associated with the fire station of the logged-in user
        $fireReports = FireReports::where('fireStationId', $adminFirefighter->fireStationId)
                                 ->latest() // Sort by the latest report
                                 ->paginate(10);
    
        return view('admin_pages.fire_reports', compact('fireReports'));
    }

    public function show($id){
        
    $report = FireReports::findOrFail($id);
    return view('admin_pages.fire_report_details', compact('report'));

    }

    public function showFinalReport($id)
{
    $report = DB::table('real_time_fire_reports')
        ->where('fire_report_id', $id)
        ->where('stage', 'final')
        ->first();

    if (!$report) {
        abort(404, 'Final report not found.');
    }

    // Decode the JSON in `content`
    $content = json_decode($report->content, true);

    // Log the decoded content
    \Log::info('Decoded Content:', ['content' => $content]);

    return view('admin_pages.final_real_time_report', [
        'firestation_name' => $content['firestation_name'] ?? 'N/A',
        'incident_type' => $content['incident_type'] ?? 'N/A',
        'location' => $content['location'] ?? 'N/A',
        'name_of_owner' => $content['name_of_owner'] ?? 'N/A',
        'alarm_status' => $content['alarm_status'] ?? 'N/A',
        'estimated_damage' => $content['estimated_damage'] ?? 0,
        'fatality' => $content['fatality'] ?? 'None',
        'injured' => $content['injured'] ?? 'None',
        'ground_commander' => $content['ground_commander'] ?? 'N/A',
        'responding_team' => $content['responding_team'] ?? [],
        'time_and_date' => $content['time_and_date'] ?? 'N/A',
        'time_of_arrival' => $content['time_of_arrival'] ?? 'N/A',
        'number_of_houses_establishments' => $content['number_of_houses_establishments'] ?? 0,
        'number_of_families_affected' => $content['number_of_families_affected'] ?? 0,
        'number_of_firetrucks' => $content['number_of_firetrucks'] ?? 0,
        'fire_out' => $content['fire_out'] ?? 'N/A',
    ]);
}

    public function reportsAndAnalytics()
    {
        // Monthly average reports
        $monthlyReports = FireReports::selectRaw('MONTH(created_at) as month, count(*) as total_reports')
                                    ->groupBy('month')
                                    ->get();

        // Total number of cases
        $totalCases = FireReports::count();

        // Average response time
        $totalResponseTime = FireReports::whereNotNull('marked_as_contained_at')
                                        ->sum(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, marked_as_contained_at)'));

        $averageResponseTime = FireReports::whereNotNull('marked_as_contained_at')
                                        ->count() > 0 ? ($totalResponseTime / FireReports::whereNotNull('marked_as_contained_at')->count()) : 0;

        // False alarm counts
        $falseAlarms = FireReports::whereNotNull('marked_as_false_alarm_by')->count();

        // Pass data to the view
        return view('admin_pages.reports_and_analytics', compact('monthlyReports', 'totalCases', 'averageResponseTime', 'falseAlarms'));
    }

    public function confirmFalseAlarm($id)
{
    $falseAlarmReport = FireReports::findOrFail($id);
    $falseAlarmReport->status = 'Confirmed False Alarm';
    $falseAlarmReport->save();

    return redirect()->route('admin.dashboard')->with('success', 'False alarm confirmed.');
}



}
