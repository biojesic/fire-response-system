<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BarangayFireAid;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\FireReports;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;

class BarangayFireAidController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        // USER fields
        'userFirstName' => 'required|string|max:255',
        'userLastName' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'userContactNumber' => 'nullable|string|max:20',
        'userAddress' => 'required|string|max:255',
        'password' => 'required|string|min:6',
        'userBirthDate' => 'nullable|date',
        // FIRE AID FIELDS
        'barangay_id' => 'required|exists:barangays,id',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'barangay_id_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'barangay_certificate_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $validated = array_map(fn($value) => is_string($value) ? trim($value) : $value, $validated);

        // 🌍 Use Google Geocoding to get lat/lng
        $latitude = null;
        $longitude = null;

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $validated['userAddress'],
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

    DB::beginTransaction();

    try {
        // 🔐 Create user first
        $user = User::create([
            'userFirstName' => $validated['userFirstName'],
            'userLastName' => $validated['userLastName'],
            'email' => $validated['email'],
            'userContactNumber' => $validated['userContactNumber'] ?? null,
            'userAddress' => $validated['userAddress'] ?? null,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'password' => Hash::make($validated['password']),
            'userBirthDate' => $validated['userBirthDate'] ?? null,
            'userStatus' => 'Active',
            'userRole' => 'Barangay',
        ]);

        // 📸 Handle file uploads
        $photoPath = null;
        $idPath = null;
        $certPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('fire_aid_photos', 'public');
        }

        if ($request->hasFile('barangay_id_path')) {
            $idPath = $request->file('barangay_id_path')->store('fire_aid_photos', 'public');
        }

        if ($request->hasFile('barangay_certificate_path')) {
            $certPath = $request->file('barangay_certificate_path')->store('fire_aid_photos', 'public');
        }

        // 🔥 Create FireAid record
        $fireAid = BarangayFireAid::create([
            'user_id' => $user->id,
            'barangay_id' => $validated['barangay_id'],
            'photo' => $photoPath,
            'barangay_id_path' => $idPath,
            'barangay_certificate_path' => $certPath,
            'position' => 'Fire Aid',
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Fire Aid registered successfully.',
            'user' => $user,
            'fire_aid' => $fireAid,
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Registration failed',
            'message' => $e->getMessage(),
        ], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show()
{
    // Get the authenticated user
    $user = Auth::user();

    // Fetch the FireAid record for the authenticated user
    $fireAid = BarangayFireAid::where('user_id', $user->id)
                              ->with(['user', 'barangay']) // Eager load user and barangay relationships
                              ->first();

    // If no FireAid record is found for the user, return an error response
    if (!$fireAid) {
        return response()->json(['message' => 'Fire Aid not found for this user'], 404);
    }

    // Return the FireAid data along with the associated user and barangay details
    return response()->json([
        'fire_aid' => $fireAid,
    ]);
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $fireAid = BarangayFireAid::find($id);

    if (!$fireAid) {
        return response()->json(['message' => 'Fire Aid not found'], 404);
    }

    $validated = $request->validate([
        'contact_number' => 'nullable|string|max:15',
        'status' => 'nullable|in:On Response,Standby,Off Duty',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'barangay_id_path' => 'nullable|string',
        'barangay_certificate_path' => 'nullable|string',
        'position' => 'nullable|string',
    ]);

    // Update FireAid fields
    $fireAid->update($validated);

    // Handle photo upload if provided
    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('fire_aid_photos', 'public');
        $fireAid->photo = $path;
    }

    $fireAid->save();

    return response()->json(['message' => 'Fire Aid updated successfully', 'data' => $fireAid]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function markAsFalseAlarm($id) {
        $report = FireReports::findOrFail($id);

        // Optional: Check if already marked
        if ($report->marked_as_false_alarm_by) {
            return response()->json(['error' => 'Already marked as false alarm'], 400);
        }

        $report->marked_as_false_alarm_by = auth()->id(); // Make sure the user is logged in
        $report->marked_as_false_alarm_at = now();
        $report->status = 'False Alarm';
        $report->save();

        $teams = Team::where('assignedFireIncident', $id)->get();

        foreach ($teams as $team) {
            // Set the assignedFireIncident to null
            $team->assignedFireIncident = null;
            $team->status = 'Standby';
            $team->save();
        }

        return response()->json(['message' => 'Marked as false alarm successfully'], 200);
    }

 public function showBarangayReportsForFireAids($barangay_id)
{
    // Get the authenticated user
    $user = Auth::user();

    // Get the logged-in user's BarangayFireAid record
    $barangayFireAid = BarangayFireAid::where('user_id', $user->id)->first();

    if (!$barangayFireAid) {
        return response()->json(['error' => 'User is not a valid Barangay Fire Aid'], 404);
    }

    // Get the barangay_id from the BarangayFireAid
    $userBarangayId = $barangayFireAid->barangay_id;

    // Check if the logged-in user's barangay_id matches the requested barangay_id
    if ($userBarangayId != $barangay_id) {
        return response()->json(['error' => 'You are not authorized to view reports for this Barangay'], 403);
    }

    // Get all Fire Aids for the given barangay_id
    $barangayFireAids = BarangayFireAid::with(['user', 'barangay'])
        ->where('barangay_id', $barangay_id)
        ->get();

    if ($barangayFireAids->isEmpty()) {
        return response()->json(['error' => 'No fire aids found for this Barangay'], 404);
    }

    // Get all Fire Reports for the same barangay_id where the status is "Pending"
    $fireReports = FireReports::where('barangay_id', $barangay_id)
        ->where('status', 'Pending') // Filter for pending status
        ->get();

    if ($fireReports->isEmpty()) {
        return response()->json(['message' => 'No pending fire reports found for this Barangay'], 404);
    }

    // Return the fire reports and the fire aids for the barangay
    return response()->json([
        // 'barangay_id' => $barangay_id,
        'fireReports' => $fireReports,
        // 'fireAids' => $barangayFireAids
    ], 200);
}
}
