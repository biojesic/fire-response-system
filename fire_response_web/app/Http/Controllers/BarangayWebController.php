<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Mail;

use App\Models\Barangay;
use Illuminate\Http\Request;
use App\Models\FireReports;
use App\Models\User;
use App\Models\FireStation;
use App\Models\CityAndMunicipality;
use App\Models\BarangayFireAid;
use App\Mail\BarangayRejectionEmail;


class BarangayWebController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Barangay::where('brgy_status', 'active')
                    ->with(['fireStation', 'lgu']);
       // 🔍 Search by barangay name
        if ($request->filled('search')) {
            $query->where('barangay_name', 'like', '%' . $request->search . '%');
        }

        // 🏙️ Filter by LGU (municipality/city)
        if ($request->filled('lgu')) {
            $query->where('lgu_id', $request->lgu);
        }

        // 🚒 Filter by fire station
        if ($request->filled('fire_station')) {
            $query->where('fire_station_id', $request->fire_station);
        }

        $barangays = $query->paginate(10);

        // Get lists for dropdown filters
        $lgus = CityAndMunicipality::orderBy('name')->get();
        $fireStations = FireStation::orderBy('firestationName')->get();

        // Count barangays waiting for verification
        $pendingCount = Barangay::where('brgy_status', 'unverified')->count();

        return view('barangay_pages.index', compact('barangays', 'lgus', 'fireStations', 'pendingCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function registerView()
    {
    $citiesAndMunicipalities = CityAndMunicipality::all();

    return view('auth_pages.register_brgy', compact('citiesAndMunicipalities'));
    }

    public function register(Request $request){

    //     dd([
    //     'env_key' => env('GOOGLE_MAPS_API_KEY'),
    //     'config_key' => config('services.google.maps_key'), // If using config
    //     'server_env' => $_ENV['GOOGLE_MAPS_API_KEY'] ?? 'Not set'
    //     'app_name' => env('APP_NAME'),
    // ]);

        $validated = $request->validate([
        'barangay_name' => 'required|string|max:255',
        'barangay_hall_address' => 'required|string',
        'contact_number' => 'nullable|string|max:15',
        'barangay_legitimacy_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:6500',
        'lgu_id' => 'required|exists:cities_and_municipalities,id',
        'userFirstName' => 'required|string|max:255',
        'userLastName' => 'required|string|max:255',
        'userContactNumber' => 'nullable|string|max:15', 
        'email' => 'required|email|unique:users,email',
        'id_image' => 'nullable|file|mimes:jpg,jpeg,png|max:6500',
        ]);

        // Handle image upload for barangay_proof
        if ($request->hasFile('barangay_legitimacy_proof')) {
            \Log::info('Attempting to store proof file', [
                'name' => $request->file('barangay_legitimacy_proof')->getClientOriginalName()
            ]);
            $barangayProofPath = $request->file('barangay_legitimacy_proof')->store('legitimacy_proof', 'public');
            \Log::info('File stored at: '.$barangayProofPath);
        }

        // Handle image upload for ID image
        if ($request->hasFile('id_image')) {
            $idImagePath = $request->file('id_image')->store('user_images', 'public');
        }

        // Debug: Check if API key is loaded
        \Log::info("Current GOOGLE_MAPS_API_KEY from .env: " . env('GOOGLE_MAPS_API_KEY'));

        $address = $validated['barangay_hall_address'];
        $geocodeData = $this->getCoordinatesByAddress($address);

            if ($geocodeData['latitude'] === null || $geocodeData['longitude'] === null) {
            return redirect()->back()->with('error', 'Unable to retrieve coordinates for the given address.');
            }

        $latitude = $geocodeData['latitude'];
        $longitude = $geocodeData['longitude'];
        // $latitude = 14.2833; 
        // $longitude = 120.8833;

        // Fetch the selected City/Municipality and its Fire Station
        $city = CityAndMunicipality::find($validated['lgu_id']);
        $fireStation = $city->fireStation;

        try {
            DB::beginTransaction();
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
            'userContactNumber' =>  $validated['userContactNumber'],
            'userRole' => 'Barangay',
            'userStatus' => 'Unverified',
        ]);

        // Temporary storage
        DB::table('barangay_pending_admins')->insert([
            'barangay_id' => $barangay->id,
            'user_id' => $adminUser->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::commit();
        return redirect()->route('home')->with('success', 'Barangay registered successfully!');
    }
    catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Registration failed: '.$e->getMessage());
        return back()->withInput()->with('error', 'Registration failed. Please try again.');
    }
}

        /**
     * Function to get latitude and longitude based on the address
     * 
     * @param string $address
     * @return array
     */
    private function getCoordinatesByAddress($address) {
        $apiKey = 'AIzaSyAnst45VUe9XkXDduDBPmuPo7H3YmWDNJ4';
        
        // Debug 1: Check if API key exists
        \Log::info("Google Maps API Key: " . ($apiKey ? "Exists" : "MISSING"));
        
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . $apiKey;
        \Log::info("Geocoding API Request URL: " . $url); // Debug 2: Log full URL
        
        try {
            $response = file_get_contents($url);
            \Log::info("API Raw Response: " . $response); // Debug 3: Log raw response
            
            $data = json_decode($response, true);
            \Log::info("API Decoded Data:", $data); // Debug 4: Log decoded data
            
            if ($data['status'] != 'OK') {
                \Log::error("Geocoding Failed - Status: " . $data['status']);
                return ['latitude' => null, 'longitude' => null];
            }
            
            $coordinates = [
                'latitude' => $data['results'][0]['geometry']['location']['lat'],
                'longitude' => $data['results'][0]['geometry']['location']['lng']
            ];
            
            \Log::info("Geocoding Success:", $coordinates);
            return $coordinates;
            
        } catch (\Exception $e) {
            \Log::error("Geocoding Error: " . $e->getMessage());
            return ['latitude' => null, 'longitude' => null];
        }
    }

    public function show($id) {
        $barangay = Barangay::with(['fireStation', 'lgu'])
                ->findOrFail($id);
        return view('barangay_pages.barangay_details', compact('barangay'));
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
        
         return view('barangay_pages.barangay_verification', compact('unverifiedBarangays'));
         
    }

    public function showBarangayVerificationDetails($id) {
        $barangay = Barangay::find($id);

        if (!$barangay) {
            return redirect()->route('barangays.index')->with('error', 'Details not found.');
        }

        $adminUser = DB::table('barangay_pending_admins')
                ->join('users', 'barangay_pending_admins.user_id', '=', 'users.id')
                ->where('barangay_pending_admins.barangay_id', $id)
                ->select('users.*')
                ->first();

        return view('barangay_pages.barangay_verification_details', [
        'barangay' => $barangay,
        'adminUser' => $adminUser
    ]);
    }

    public function approveBarangay($barangayId) {
        
            DB::transaction(function () use ($barangayId) {
            // 1. Approve barangay
            Barangay::where('id', $barangayId)->update([
                'brgy_status' => 'active',
                'rejection_reason' => null,
                'rejected_by' => null,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            
            // 2. Get pending admin
            $pending = DB::table('barangay_pending_admins')
                    ->where('barangay_id', $barangayId)
                    ->firstOrFail();

            $user = User::findOrFail($pending->user_id);
        
            // 3. Approve user and get the image path
            $idImagePath = $user->id_image;
            
            User::where('id', $pending->user_id)->update([
                'userStatus' => 'Active',
                'rejection_reason' => null,
                'reapply_allowed' => false,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'id_image' => null,
            ]);
            
            // 4. Create official record
            BarangayFireAid::create([
                'user_id' => $pending->user_id,
                'barangay_id' => $barangayId,
                'status' => "Standby",
                'barangay_id_path' => $idImagePath,
                'position' => "Admin"
            ]);
            
            // 5. Cleanup
            DB::table('barangay_pending_admins')->where('id', $pending->id)->delete();
        });

        return redirect()->route('barangay.verification')->with('success', 'Barangay account has been approved.');

    }

    public function rejectBarangay(Request $request, $barangayId) {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($barangayId, $validated) {
                // 1. Get barangay record
                $barangay = Barangay::lockForUpdate()->findOrFail($barangayId);

                // 2. Update barangay status
                $barangay->update([
                    'brgy_status' => 'rejected',
                    'rejection_reason' => $validated['rejection_reason'],
                    'rejected_by' => auth()->id(),
                    'rejected_at' => now(),
                    'reapply_allowed' => true,
                ]);

                // 3. Find and update associated admin user
                $pending = DB::table('barangay_pending_admins')
                        ->where('barangay_id', $barangayId)
                        ->first();

                if ($pending) {
                    $user = User::findOrFail($pending->user_id);
                    
                    // Update user status
                    $user->update([
                        'userStatus' => 'Rejected',
                        'rejection_reason' => 'Barangay application rejected.',
                        'last_rejection_at' => now(),
                        'rejected_by' => auth()->id(),
                        'reapply_allowed' => true,
                    ]);

                    // =============================================
                    // 4. [NEW CODE] TOKEN GENERATION AND STORAGE
                    // =============================================
                    $token = Str::random(64);
                    DB::table('reapplication_tokens')->insert([
                        'token' => $token,
                        'user_id' => $user->id,
                        'user_role' => 'barangay_admin',
                        'rejection_reason' => $validated['rejection_reason'],
                        'expires_at' => now()->addDays(7), // 7 days validity
                        'created_at' => now(),
                    ]);
                    // =============================================

                    // 5. Send email with token
                    Mail::to($user->email)->send(new BarangayRejectionEmail(
                        $barangay->barangay_name,
                        $validated['rejection_reason'],
                        true,
                        $token
                    ));
                }
            });

            return redirect()->route('barangay.verification')
                ->with('warning', 'Barangay application has been rejected');

        } catch (\Exception $e) {
            Log::error("Barangay rejection failed: ".$e->getMessage());
            return back()->with('error', 'Failed to reject application');
        }
    }

    public function reapply(Request $request)
{
    $user = auth()->user();
    
    // Hanapin ang rejected barangay
    $barangay = Barangay::whereHas('pendingAdmin', function($q) use ($user) {
        $q->where('user_id', $user->id);
    })->where('brgy_status', 'rejected')->firstOrFail();

    if (!$barangay->reapply_allowed) {
        return back()->with('error', 'You are not allowed to reapply');
    }

    // I-update ang status
    $barangay->update([
        'brgy_status' => 'pending',
        'rejection_reason' => null,
        'reapplication_count' => $barangay->reapplication_count + 1
    ]);

    // Padalhan ng confirmation email
    $user->notify(new BarangayReappliedNotification());

    return back()->with('success', 'Reapplication submitted for review');
}
}
