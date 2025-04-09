<?php

namespace App\Http\Controllers;

use App\Models\Firefighter;
use App\Models\User;
use Illuminate\Http\Request;

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
            'position' => 'required|in:Team Leader,Responder,Dispatcher', // Position can be one of these
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
    public function show($id)
    {
        // Fetch firefighter by ID with their relationships
        return Firefighter::with(['user', 'fireStation', 'team'])->findOrFail($id);
    }

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
            'position' => 'required|in:Team Leader,Responder,Dispatcher, Admin',
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
        // Require authentication
        $admin = $request->user();

        if (!$admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get admin position & fireStationId in one query
        $adminFirefighter = Firefighter::where('userId', $admin->id)->first();

        if (!$adminFirefighter || $adminFirefighter->position !== 'Admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$adminFirefighter->fireStationId) {
            return response()->json(['error' => 'Admin has no assigned fire station'], 400);
        }

        // Validate request
        $fields = $request->validate([
            'userFirstName' => 'required|string|max:255',
            'userLastName' => 'required|string|max:255',
            'userEmail' => 'required|string|email|max:255|unique:users',
            'userContactNumber' => 'required|string|max:20',
            'userAddress' => 'required|string|max:100000',
            'userAge' => 'required|integer',
            'userPassword' => 'required|string|min:8|confirmed',
            'teamId' => 'required|exists:teams,id',
            'position' => 'required|string|in:Team Leader,Responder,Dispatcher,Admin',
            'personalEquipment' => 'nullable|array',
        ]);

        // Trim all string inputs
        $fields = array_map(fn($value) => is_string($value) ? trim($value) : $value, $fields);

        // Create the user in the users table
        $user = User::create([
            'userFirstName' => $fields['userFirstName'],
            'userLastName' => $fields['userLastName'],
            'userEmail' => $fields['userEmail'],
            'userContactNumber' => $fields['userContactNumber'],
            'userAddress' => $fields['userAddress'],
            'userAge' => $fields['userAge'],
            'userPassword' => bcrypt($fields['userPassword']),
            'userRole' => 'firefighter',
            'userStatus' => 'Active'
        ]);

        // Create firefighter entry
        Firefighter::create([
            'userId' => $user->id,
            'fireStationId' => $adminFirefighter->fireStationId, // Get from the authenticated admin
            'teamId' => $fields['teamId'],
            'position' => $fields['position'],
            'personalEquipment' => is_array($fields['personalEquipment']) ? $fields['personalEquipment'] : json_decode($fields['personalEquipment'], true),

        ]);

        return response()->json([
            'message' => 'Firefighter registered successfully',
            'user' => $user
        ], 201);
    }

    public function getTeamStatus($firefighterId)
    {
        $firefighter = Firefighter::with('team')->findOrFail($firefighterId);

        if (!$firefighter->team) {
            return response()->json(['status' => 'No Team Assigned'], 404);
        }

        return response()->json(['status' => $firefighter->team->status], 200);
    }

    public function getDetails($id)
    {
        $firefighter = Firefighter::with('team')  // Assuming 'team' is a relationship
            ->where('userId', $id)
            ->first();

        if (!$firefighter) {
            return response()->json(['error' => 'Firefighter not found'], 404);
        }

        return response()->json($firefighter);
    }
}
