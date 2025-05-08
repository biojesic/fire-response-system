<?php

namespace App\Http\Controllers;

use App\Models\Firefighter;
use App\Models\FirefighterPosition;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AssignedIncident;
use App\Models\FireReports;
use App\Models\FirefighterReports;


class FirefighterController extends Controller
{
    public function index()
    {
        // Fetch all firefighters with their relationships
        return Firefighter::with(['user', 'fireStation', 'team'])->get();
    }

    /**
     * Store a newly created firefighter in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming data
        $request->validate([
            'userId' => 'required|exists:users,id', // Reference to users table
            'fireStationId' => 'required|exists:fire_station,id', // Reference to fire_station table
            'teamId' => 'nullable|exists:teams,id', // Nullable, reference to teams table
            'position_id' => 'required|exists:firefighter_positions,id',
            'personal_equipment' => 'nullable|array', // List of personal equipment (optional)
        ]);

        // Create a new firefighter record
        $firefighter = Firefighter::create($request->all());

        // Return the created firefighter with a 201 status
        return response()->json($firefighter, 201);
    }

    /**
     * Display the specified firefighter.
     */
    // public function show($id)
    // {
    //     // Fetch firefighter by ID with their relationships
    //     return Firefighter::with(['user', 'fireStation', 'team'])->findOrFail($id);
    // }

    // public function show($id)
    // {
    //     $firefighter = Firefighter::with([
    //         'respondedFireReports' => function ($query) {
    //             $query->select(
    //                 'fire_reports.id',
    //                 'fire_reports.location',
    //                 'fire_reports.landmark',
    //                 'fire_reports.description',
    //                 'fire_reports.status',
    //                 'fire_reports.created_at',
    //                 'fire_reports.marked_as_contained_by_id',
    //                 'fire_reports.marked_as_contained_at'
    //             );
    //         }
    //     ])->findOrFail($id);
    
    //     return response()->json([
    //         'fireReports' => $firefighter->respondedFireReports
    //     ]);
    // }
    

    /**
     * Update the specified firefighter in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate incoming data
        $request->validate([
            'userId' => 'required|exists:users,id',
            'fireStationId' => 'required|exists:fire_station,id',
            'teamId' => 'nullable|exists:teams,id',
            'position_id' => 'required|exists:firefighter_positions,id',
            'personal_equipment' => 'nullable|array',
        ]);

        // Find the firefighter by ID
        $firefighter = Firefighter::findOrFail($id);

        // Update the firefighter's data
        $firefighter->update($request->all());

        // Return the updated firefighter
        return response()->json($firefighter);
    }

    /**
     * Remove the specified firefighter from storage.
     */
    public function destroy($id)
    {
        // Find the firefighter by ID
        $firefighter = Firefighter::findOrFail($id);

        // Delete the firefighter record
        $firefighter->delete();

        // Return a success message
        return response()->json(['message' => 'Firefighter deleted successfully']);
    }

    // public function updateStatus(Request $request)
    // {
    //     $request->validate([
    //         'firefighter_id' => 'required|exists:firefighters,id',
    //         'status' => 'required|in:On Response,StandBy,Off Duty',
    //     ]);

    //     $firefighter = Firefighter::find($request->firefighter_id);
    //     $firefighter->status = $request->status;
    //     $firefighter->save();

    //     return response()->json(['message' => 'Status updated successfully']);
    // }

    public function registerFirefighter(Request $request)
    {
        $admin = $request->user();

        if (!$admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $adminFirefighter = Firefighter::where('userId', $admin->id)->first();

        if (!$adminFirefighter || !$adminFirefighter->fireStationId) {
            return response()->json(['error' => 'Unauthorized or no assigned fire station'], 403);
        }

        $fields = $request->validate([
            'userFirstName' => 'required|string|max:255',
            'userLastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'userContactNumber' => 'required|string|max:20',
            'userAddress' => 'required|string|max:100000',
            'userBirthDate' => 'required|date|before:today',
            'password' => 'required|string|min:8|confirmed',
            'teamId' => 'required|exists:teams,id',
            'position_id' => 'required|exists:firefighter_positions,id',
            'rank_id' => 'required|exists:firefighter_ranks,id',
            'personalEquipment' => 'nullable|array',
        ]);

        $fields = array_map(fn($value) => is_string($value) ? trim($value) : $value, $fields);

        // 🌍 Use Google Geocoding to get lat/lng
        $latitude = null;
        $longitude = null;

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $fields['userAddress'],
                'key' => env('GOOGLE_MAPS_API_KEY'),
            ]);

            if ($response->successful() && isset($response['results'][0]['geometry']['location'])) {
                $location = $response['results'][0]['geometry']['location'];
                $latitude = $location['lat'];
                $longitude = $location['lng'];
            }
        } catch (\Exception $e) {
            Log::warning('Geocoding failed during firefighter registration: ' . $e->getMessage());
        }

        // 👨‍🚒 Create user
        $user = User::create([
            'userFirstName' => $fields['userFirstName'],
            'userLastName' => $fields['userLastName'],
            'email' => $fields['email'],
            'userContactNumber' => $fields['userContactNumber'],
            'userAddress' => $fields['userAddress'],
            'userBirthDate' => $fields['userBirthDate'],
            'password' => bcrypt($fields['password']),
            'userRole' => 'firefighter',
            'userStatus' => 'Active',
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        Firefighter::create([
            'userId' => $user->id,
            'fireStationId' => $adminFirefighter->fireStationId,
            'teamId' => $fields['teamId'],
            'position_id' => $fields['position_id'],
            'rank_id' => $fields['rank_id'],
            'personalEquipment' => is_array($fields['personalEquipment']) ? $fields['personalEquipment'] : json_decode($fields['personalEquipment'], true),
        ]);

        return response()->json([
            'message' => 'Firefighter registered successfully',
            'user' => $user
        ], 201);
    }

    public function getStatus($firefighterId)
    {
        $firefighter = Firefighter::with('team')->findOrFail($firefighterId);
    
        // Always check firefighter's own status first
        if (strtolower($firefighter->status) === 'off duty') {
            return response()->json(['status' => 'Off Duty'], 200);
        }
    
        // If they're not Off Duty, check their team status
        if (!$firefighter->team) {
            return response()->json(['status' => 'No Team Assigned'], 200);
        }
    
        return response()->json(['status' => $firefighter->team->status], 200);
    }


    public function getDetails($id)
    {
        $firefighter = Firefighter::with(['team', 'position'])
            ->where('userId', $id)
            ->first();

        if (!$firefighter) {
            return response()->json(['error' => 'Firefighter not found'], 404);
        }

        return response()->json($firefighter);
    }

    public function getPositions()
    {
        return response()->json(FirefighterPosition::all());
    }

    public function getAssignedFireReports(Request $request)
{
    $user = $request->user();

    $firefighter = Firefighter::where('userId', $user->id)->first();

    if (!$firefighter) {
        return response()->json(['message' => 'No firefighter found for this user.'], 404);
    }

    $assignedReports = $firefighter->firefighterReports()->with('fireReport')->get();

    // Optional: transform results
    $response = $assignedReports->map(function ($report) {
        return [
            'report_id' => $report->fireReport->id,
            'location' => $report->fireReport->location,
            'landmark' => $report->fireReport->landmark,
            'status' => $report->fireReport->status,
            'created_at' => $report->fireReport->created_at,
        ];
    });

    return response()->json($response);
}

}
