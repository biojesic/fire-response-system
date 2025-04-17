<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Firefighter;
use App\Models\Team;
use App\Models\FirefighterPosition;
use App\Http\Controllers\AuthWebController;
use App\Models\User;
// use App\Controllers\FirefighterWebControllers;

class FirefighterWebController extends Controller
{

    public function showRegistrationForm()
    {
        // dd('reg');
        // Fetch the firefighter record for the authenticated user
        $firefighter = auth()->user()->firefighter;

        // Ensure the firefighter has an associated fire station
        if (!$firefighter || !$firefighter->fireStation) {
            // Handle the case where the firefighter doesn't have a fire station (redirect, show an error, etc.)
            return redirect()->route('admin.dashboard')->withErrors('No fire station assigned to this firefighter.');
        }

        // Fetch the teams associated with the fire station of the firefighter
        $teams = $firefighter->fireStation->teams;

        // Fetch the positions for the firefighter
        $positions = FirefighterPosition::all();
        // dd($teams, $positions);
        return view('admin_pages.register_firefighter', compact('teams', 'positions'));
    }

    

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function registerFirefighter(Request $request)
{
    $admin = $request->user();

    if (!$admin) {
        return redirect()->back()->with('error', 'Unauthorized access.');
    }

    $adminFirefighter = Firefighter::where('userId', $admin->id)->first();

    if (!$adminFirefighter || !$adminFirefighter->fireStationId) {
        return redirect()->back()->with('error', 'Unauthorized or no assigned fire station.');
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
        'personalEquipment' => 'nullable|array',
    ], [
        'userFirstName.required' => 'The first name field is required.',
        'userLastName.required' => 'The last name field is required.',
        'userAddress.required' => 'The address field is required.',
        'userBirthDate.required' => 'Birth date is required.',
        'userContactNumber.required' => 'Contact number field is required.',
        'teamId.required' => 'The team field is required.',
        'position_id.required' => 'The position field is required.',
    ]);

    $fields = array_map(fn($value) => is_string($value) ? trim($value) : $value, $fields);

    // 🌍 Geocoding
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
        'personalEquipment' => is_array($fields['personalEquipment']) ? $fields['personalEquipment'] : json_decode($fields['personalEquipment'], true),
    ]);

    return redirect()->route('admin.firefighters')->with('success', 'Firefighter registered successfully.');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
