<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Firefighter;
use App\Models\Team;
use App\Models\FirefighterPosition;
use App\Http\Controllers\AuthWebController;
use App\Models\User;
use App\Http\Controllers\PersonalEquipmentController;
use App\Models\PersonalEquipment;
use App\Models\FirefighterRank;

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

        $equipmentOptions = PersonalEquipment::all();

        $ranks = FirefighterRank::all();

        return view('admin_pages.register_firefighter', compact('teams', 'positions', 'equipmentOptions', 'ranks'));
    }

    public function index(Request $request)
    {
        $admin = auth()->user();
        $adminFirefighter = $admin->firefighter;
    
        if (!$adminFirefighter || !$adminFirefighter->fireStationId) {
            return redirect()->route('admin.dashboard')->withErrors('No fire station assigned.');
        }
    
        $query = Firefighter::with(['user', 'team', 'position', 'rank'])
            ->where('fireStationId', $adminFirefighter->fireStationId);
    
        // 🔍 Apply search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('user', function ($q) use ($searchTerm) {
                $q->where('userFirstName', 'like', "%{$searchTerm}%")
                  ->orWhere('userLastName', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }
    
        // 🏷️ Apply filter by team
        if ($request->filled('team')) {
            $query->where('teamId', $request->team);
        }
        
         // 🎖️ Apply filter by rank
        if ($request->filled('rank_id')) {
            $query->where('rank_id', $request->rank_id); // Filter by rank
        }
        
        $firefighters = $query->paginate(10)->withQueryString();
    
        // For team filter dropdown
        $teams = $adminFirefighter->fireStation->teams;
        $ranks = FirefighterRank::all();
    
        return view('admin_pages.firefighters', compact('firefighters', 'teams', 'ranks'));
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
    public function registerFirefighter(Request $request) {
    // Get the authenticated admin user
    $admin = $request->user();

    // Check if the admin exists
    if (!$admin) {
        return redirect()->back()->with('error', 'Unauthorized access.');
    }

    // Check if the admin firefighter has an assigned fire station
    $adminFirefighter = Firefighter::where('userId', $admin->id)->first();
    if (!$adminFirefighter || !$adminFirefighter->fireStationId) {
        return redirect()->back()->with('error', 'Unauthorized or no assigned fire station.');
    }

    // Validate the request fields
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
        'rank_id' => 'nullable|exists:firefighter_ranks,id',
        'personalEquipment' => 'nullable|array',
        'personalEquipment.*' => 'exists:personal_equipment,id', // Ensure each equipment exists
    ], [
        'userFirstName.required' => 'The first name field is required.',
        'userLastName.required' => 'The last name field is required.',
        'userAddress.required' => 'The address field is required.',
        'userBirthDate.required' => 'Birth date is required.',
        'userContactNumber.required' => 'Contact number field is required.',
        'teamId.required' => 'The team field is required.',
        'position_id.required' => 'The position field is required.',
    ]);

    // Clean up extra spaces
    $fields = array_map(fn($value) => is_string($value) ? trim($value) : $value, $fields);

    // 🌍 Geocoding (for address)
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

    // 👨‍🚒 Create the user (firefighter)
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

    // Create the firefighter record
    $firefighter = Firefighter::create([
        'userId' => $user->id,
        'fireStationId' => $adminFirefighter->fireStationId,
        'teamId' => $fields['teamId'],
        'position_id' => $fields['position_id'],
        'rank_id' => $fields['rank_id'],
    ]);

    // If personal equipment is provided, attach it via the pivot table
    if ($request->has('personalEquipment')) {
        // Attach the personal equipment to the firefighter through the pivot table
        $firefighter->equipment()->attach($fields['personalEquipment']);
    }

    // Redirect back with success message
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
