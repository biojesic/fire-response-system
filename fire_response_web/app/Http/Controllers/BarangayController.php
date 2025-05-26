<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use Illuminate\Http\Request;
use App\Models\FireReports;
use Illuminate\Support\Facades\DB;

class BarangayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         $fireStations = FireStation::all(); // Get all fire stations
        // return view('barangays.create', compact('fireStations')); // Pass fire stations to the view
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
        'barangay_name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'contact_number' => 'nullable|string|max:15',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'fire_station_id' => 'nullable|exists:fire_station,id',
        'barangay_legitimacy_proof' => 'nullable|string|max:255',
        ]);

        Barangay::create($validated);

        // return redirect()->route('barangays.index')->with('success', 'Barangay created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Barangay $barangay)
    {
        // return view('barangays.show', compact('barangay')); // Display the barangay details
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barangay $barangay)
    {
    //     $fireStations = FireStation::all(); // Get all fire stations
    // return view('barangays.edit', compact('barangay', 'fireStations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Barangay $barangay)
    {
        // Validate the data
    $validated = $request->validate([
        'barangay_name' => 'required|string|max:255',
        'address' => 'required|string|max:255',
        'contact_number' => 'nullable|string|max:15',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        'fire_station_id' => 'nullable|exists:fire_stations,id', // Ensure the fire station exists
    ]);

    $barangay->update($validated);

    // Redirect after updating
    // return redirect()->route('barangays.index')->with('success', 'Barangay updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barangay $barangay)
    {
       $barangay->delete(); // Delete the barangay record

    // return redirect()->route('barangays.index')->with('success', 'Barangay deleted successfully.');
    }

    public function showBrgyDashboard()
    {

        // Get the logged-in user
    $user = auth()->user();

    // Check if the user role is 'barangay'
    if ($user->userRole !== 'Barangay') {
        abort(403, 'Unauthorized'); // If not a barangay admin, abort with an error
    }

    // Retrieve the barangay_id from the barangay_fire_aids table for the logged-in user
    $barangayId = DB::table('barangay_fire_aids')
                    ->where('user_id', $user->id) // Get barangay_id by user_id
                    ->pluck('barangay_id') // Get the specific barangay_id
                    ->first(); // We only expect one barangay_id per user

    $barangayName = DB::table('barangays')
                      ->where('id', $barangayId)
                      ->pluck('barangay_name')
                      ->first();

    // Check if the barangay_id is found
    if (!$barangayId) {
        abort(404, 'Barangay not found for this user');
    }

        $totalReports = FireReports::where('barangay_id', $barangayId)->count();
        $newReports = FireReports::where('created_at', '>=', now()->subDay())
            ->where('barangay_id', $barangayId)
            ->count();
        $resolvedReports = FireReports::where('status', 'Fire Out')
            ->where('barangay_id', $barangayId)
            ->count();
         $falseAlarms = FireReports::whereNotNull('marked_as_false_alarm_by')
                              ->where('barangay_id', $barangayId)
                              ->count();
        $activeFires = FireReports::whereIn('status', ['Pending', 'On Response'])
                              ->where('barangay_id', $barangayId)
                              ->count();

         // Calculate the average response time for the specific barangay
    $totalResponseTime = FireReports::whereNotNull('marked_as_contained_at')
                                    ->where('barangay_id', $barangayId)
                                    ->sum(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, marked_as_contained_at)'));

    $averageResponseTime = FireReports::whereNotNull('marked_as_contained_at')
                                      ->where('barangay_id', $barangayId)
                                      ->count() > 0 
                                      ? ($totalResponseTime / FireReports::whereNotNull('marked_as_contained_at')
                                                                      ->where('barangay_id', $barangayId)
                                                                      ->count()) 
                                      : 0;

        // Get the most recent reports
        $recentReports = FireReports::latest()->take(5)->get();

        return view('barangay_pages.barangay_dashboard', [
        'totalReports' => $totalReports,
        'newReports' => $newReports,
        'resolvedReports' => $resolvedReports,
        'falseAlarms' => $falseAlarms,
        'activeFires' => $activeFires,
        'averageResponseTime' => $averageResponseTime,
        'recentReports' => $recentReports,
        'user' => $user,
        'barangayName' => $barangayName, 

    ]);
    }

}
