<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use Illuminate\Http\Request;
use App\Models\FireReports;
use App\Models\User;
use App\Models\CityAndMunicipality;
use Illuminate\Support\Facades\DB;

class BarangayWebController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangays = Barangay::where('brgy_status', 'active')
        ->with(['fireStation', 'lgu'])->paginate(10);
        return view('barangay_pages.index', compact('barangays'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    $citiesAndMunicipalities = CityAndMunicipality::all();

    return view('auth_pages.register_brgy', compact('citiesAndMunicipalities'));
    }

    public function store(Request $request){
        $validated = $request->validate([
        'barangay_name' => 'required|string|max:255',
        'barangay_hall_address' => 'required|string',
        'contact_number' => 'nullable|string|max:15',
        'barangay_legitimacy_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:6500',
        'lgu_id' => 'required|exists:cities_and_municipalities,id',
        'userFirstName' => 'required|string|max:255',
        'userLastName' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'id_image' => 'nullable|file|mimes:jpg,jpeg,png|max:6500',
        ]);

        // Handle image upload for barangay_proof
        if ($request->hasFile('barangay_legitimacy_proof')) {
            $barangayProofPath = $request->file('barangay_legitimacy_proof')->store('legitimacy_proof', 'public');
        }

        // Handle image upload for ID image
        if ($request->hasFile('id_image')) {
            $idImagePath = $request->file('id_image')->store('user_images', 'public');
        }

        $address = $validated['barangay_hall_address'];
        $geocodeData = $this->getCoordinatesByAddress($address);

            if ($geocodeData['latitude'] === null || $geocodeData['longitude'] === null) {
            return redirect()->back()->with('error', 'Unable to retrieve coordinates for the given address.');
            }

        $latitude = $geocodeData['latitude'];
        $longitude = $geocodeData['longitude'];

        // Fetch the selected City/Municipality and its Fire Station
        $city = CityAndMunicipality::find($validated['lgu_id']);
        $fireStation = $city->fireStation;

        $barangay = Barangay::create([
            'barangay_name' => $validated['barangay_name'],
            'barangay_hall_address' => $validated['barangay_hall_address'],
            'contact_number' => $validated['contact_number'],
            'latitude' => $latitude,
            'longitude' => $longitude,
            'fire_station_id' => $fireStation ? $fireStation->id : null,
            'barangay_legitimacy_proof' => $barangayProofPath ?? null,
            'brgy_status' => 'unverified',
            'lgu_id' => $validated['lgu_id'],
        ]);

        $adminPassword = bcrypt('barangayadmin');

        $adminUser = User::create([
            'userFirstName' => $validated['userFirstName'],
            'userLastName' => $validated['userLastName'],
            'email' => $validated['email'],
            'password' => $adminPassword, 
            'id_image' => $idImagePath ?? null,
            'userRole' => 'Barangay',
            'userStatus' => 'Unverified',
        ]);

        return redirect()->route('home')->with('success', 'Barangay registered successfully!');
    }

        /**
     * Function to get latitude and longitude based on the address
     * 
     * @param string $address
     * @return array
     */
    private function getCoordinatesByAddress($address)
    {

        $apiKey = env('GOOGLE_MAPS_API_KEY');

        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . $apiKey;

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if ($data['status'] == 'OK') {
            $latitude = $data['results'][0]['geometry']['location']['lat'];
            $longitude = $data['results'][0]['geometry']['location']['lng'];
            
            return ['latitude' => $latitude, 'longitude' => $longitude];
        }

        return ['latitude' => null, 'longitude' => null];
    }

    public function show(Barangay $barangay)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Barangay $barangay)
    {

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
        'fire_station_id' => 'nullable|exists:fire_stations,id',
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

    public function barangayVerificationPage() {
        $unverifiedBarangays = Barangay::where('brgy_status', 'unverified')
            ->get();
        
         return view('barangay_pages.barangay_verification_page', compact('unverifiedBarangays'));
         
    }
}
