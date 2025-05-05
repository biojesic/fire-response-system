<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FireReports;
use App\Models\RealTimeFireReport;

class RealTimeFireReportWebController extends Controller {
        public function index($fireReportId)
        {
            // $fireReports = RealTimeFireReport::where('fire_report_id', $fireReportId)->get();
            // return view('admin.realtime-fire-reports.index', compact('fireReports'));
        }
    
        // Create a new real-time fire report (form to create report)
        public function create($fireReportId)
        {
            // $fireReport = FireReport::findOrFail($fireReportId);
            // return view('admin.realtime-fire-reports.create', compact('fireReport'));
        }
    
        // Store a new real-time fire report
        public function store(Request $request, $fireReportId)
        {
            // Validate the incoming data
            $validated = $request->validate([
                'stage' => 'required|string',
                'content' => 'required|array', // The content must be an array (can be JSON)
            ]);
    
            // Create the real-time fire report
            $realTimeFireReport = RealTimeFireReport::create([
                'fire_report_id' => $fireReportId,
                'stage' => $validated['stage'],
                'content' => $validated['content'],
            ]);
    
            // Redirect back with a success message
            return redirect()->route('admin.realtime-fire-reports.index', $fireReportId)
                             ->with('success', 'Real-time fire report created successfully.');
        }
    
        // Show a specific real-time fire report (for editing, etc.)
        public function show($id)
        {
            // $realTimeFireReport = RealTimeFireReport::find($id);
    
            // if (!$realTimeFireReport) {
            //     return response()->json(['message' => 'Report not found'], 404);
            // }
    
            // return view('admin.realtime-fire-reports.show', compact('realTimeFireReport'));
        }
    
        // Edit a real-time fire report (optional)
        public function edit($id)
        {
        //     $realTimeFireReport = RealTimeFireReport::findOrFail($id);
        //     return view('admin.realtime-fire-reports.edit', compact('realTimeFireReport'));
        // }
    
        // // Update a real-time fire report
        // public function update(Request $request, $id)
        // {
        //     $realTimeFireReport = RealTimeFireReport::findOrFail($id);
    
        //     $validated = $request->validate([
        //         'stage' => 'required|string',
        //         'content' => 'required|array',
        //     ]);
    
        //     $realTimeFireReport->update($validated);
    
        //     return redirect()->route('admin.realtime-fire-reports.index', $realTimeFireReport->fire_report_id)
        //                      ->with('success', 'Real-time fire report updated successfully.');
        }
    
        // Delete a real-time fire report
        public function destroy($id)
        {
            $realTimeFireReport = RealTimeFireReport::findOrFail($id);
            $realTimeFireReport->delete();
    
            return redirect()->route('admin.realtime-fire-reports.index', $realTimeFireReport->fire_report_id)
                             ->with('success', 'Real-time fire report deleted successfully.');
        }

        public function showInitial($id) {
            $report = FireReports::findOrFail($id);
            // Get the logged-in user (assuming you're using Laravel's built-in Auth system)
            $user = auth()->user();

            // Get the user's fire station name (assuming the User model has a relationship with FireStation)
            $firestationName = $user->firefighter->firestation->firestationName;
            $firestationContactNum = $user->firefighter->firestation->firestationContactNumber; 

            // Get the exact location (this should be available in the FireReports model)
            $location = $report->location;

            // Get the time and date reported (from created_at)
            $timeAndDateReported = $report->created_at;

            // Get the sender's name and rank (from Users and Firefighters tables)
            $senderFirstName = $user->userFirstName;
            $senderLastName = $user->userLastName;
            $userRank = $user->firefighter->rank->rank_name ? $user->firefighter->rank->rank_name : 'No Rank';

            $senderInfo = $userRank . ' ' . $senderFirstName . ' ' . $senderLastName . ' - ' . $firestationContactNum;

            // $realTimeReport = RealTimeFireReport::where('fire_report_id', $id)->first();
            // $stage = $realTimeReport ? $realTimeReport->stage : null;

            // dd($firestationName, $senderInfo, $location, $timeAndDateReported);

            return view('admin_pages.initial_report_form', compact(
                'report',
                'firestationName',
                'location', 
                'timeAndDateReported', 
                'senderInfo'
                
            ));
        }

        public function createInitial($id, Request $request) {
            // dd($request->all());
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

            // Create the content as a JSON object from the form data
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
                'sender' => $request->sender_info,
            ];

            // Store the data in the real_time_fire_reports table
            $fireReport = new RealTimeFireReport();
            $fireReport->fire_report_id = $id;
            $fireReport->stage = 'initial';
            $fireReport->content = json_encode($content); // Store the content as JSON
            $fireReport->save();

            // Redirect with success message
            return redirect()->route('admin.dashboard')
                ->with('success', 'Fire report has been submitted successfully.');
        }

        
        public function createProgressive($id) {
            $report = FireReports::findOrFail($id);
            return view('admin_pages.progress_report_form', compact('report'));
        }
        
        public function createFinal($id) {
            $report = FireReports::findOrFail($id);
            return view('admin_pages.final_report_form', compact('report'));
        }
}
