<?php

namespace App\Http\Controllers;

use App\Models\FireReports;
use App\Models\FireStation;
use App\Models\Team;
use App\Models\User;
use App\Models\Firefighter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Models\FireFighterReports;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use App\Models\Barangay;
use App\Services\FirebaseService;



class FireReportsController extends Controller
{
    protected $firebaseService;

    // Correct constructor syntax
    public function __construct(FirebaseService $firebaseService) {
        $this->firebaseService = $firebaseService;
    }

    public function show($id)
    {
        // Find the fire report by its ID
        $fireReport = FireReports::find($id);
    
        // Check if the fire report exists
        if (!$fireReport) {
            return response()->json(['message' => 'Fire report not found'], 404);
        }
    
        // Return the details of the fire report
        return response()->json([
            'id' => $fireReport->id,
            'reported_by' => $fireReport->reported_by,
            'fireStationId' => $fireReport->fireStationId,
            'location' => $fireReport->location,
            'latitude' => $fireReport->latitude,
            'longitude' => $fireReport->longitude,
            'landmark' => $fireReport->landmark,
            'description' => $fireReport->description,
            'contact_info' => $fireReport->contact_info,
            'status' => $fireReport->status,
            'created_at' => $fireReport->created_at,
            'updated_at' => $fireReport->updated_at,

            'marked_as_false_alarm_by' => $fireReport->marked_as_false_alarm_by,
            'marked_as_false_alarm_at' => $fireReport->marked_as_false_alarm_at,
            'marked_as_contained_by_id' => $fireReport->marked_as_contained_by_id,
            'marked_as_contained_at' => $fireReport->marked_as_contained_at
        ]);
    }

    
    private function getCoordinatesFromAddress($address)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&key=" . $apiKey;

        Log::info("Geocoding API request: " . $url);

        try {
            $response = Http::get($url);
            $data = $response->json();

            if ($data['status'] == 'OK') {
                return [
                    'latitude' => $data['results'][0]['geometry']['location']['lat'],
                    'longitude' => $data['results'][0]['geometry']['location']['lng']
                ];
            }
        } catch (\Exception $e) {
            Log::error("Geocoding API request error: " . $e->getMessage());
        }
        return null;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function findNearestFireStation($latitude, $longitude)
    {
        $fireStations = FireStation::all();
        $nearestStation = null;
        $shortestDistance = PHP_INT_MAX;

        foreach ($fireStations as $fireStation) {
            $distance = $this->calculateDistance($latitude, $longitude, $fireStation->latitude, $fireStation->longitude);
            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestStation = $fireStation;
            }
        }
        return $nearestStation;
    }

    private function findNearestBarangay($latitude, $longitude) {
        // Fetch all Barangay records (assuming you have a Barangay model)
        $barangays = Barangay::all(); 

        $nearestBarangay = null;
        $shortestDistance = PHP_INT_MAX;

        foreach ($barangays as $barangay) {
            // Calculate the distance between the provided coordinates and each Barangay's coordinates
            $distance = $this->calculateDistance($latitude, $longitude, $barangay->latitude, $barangay->longitude);

            // Check if the current Barangay is closer
            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestBarangay = $barangay;
            }
        }

        // Return the nearest Barangay's ID
        return $nearestBarangay ? $nearestBarangay->id : null;
    }


    private function sendNotificationToFireAid($fireAid){
        // Firebase Messaging setup
        $firebase = (new Factory)->createMessaging();
        
        // Get the FCM Token from the Fire Aid
        $fcmToken = $fireAid->fcm_token;

        if ($fcmToken) {
            // Create notification message
            $message = CloudMessage::new()
                ->withTarget('token', $fcmToken)  // Send to this Fire Aid's FCM token
                ->withNotification([
                    'title' => 'New Fire Report',  // Notification title
                    'body' => 'A new fire incident has been reported near your area. Please respond immediately.'  // Notification body
                ]);

            // Send notification
            $firebase->send($message);
        }
    }

    private function sendFireReportNotificationToCivilians($fireReport)
{
    // Get all civilian users with an FCM token
    $civilianUsers = User::where('userRole', 'civilian')->whereNotNull('fcm_token')->get();

    // Prepare notification details
    $title = 'Fire Alert';
    $body = 'There is a new fire report in your area. Please be aware and stay safe.';
    $data = [
        'fire_report_id' => $fireReport->id,  // Additional data for the notification
    ];

    // Send notification to all civilians
    foreach ($civilianUsers as $user) {
        if (!empty($user->fcm_token)) {
            $this->firebaseService->sendNotification($user->fcm_token, $title, $body, $data);
        }
    }
}

//     // Helper method to send notifications
// private function sendFireReportNotificationToCivilians($fireReport)
// {
//     // Get all civilian users
//     $civilianUsers = User::where('userRole', 'civilian')->get();

//     // Prepare notification details
//     $title = 'Fire Alert';
//     $body = 'There is a new fire report in your area. Please be aware and stay safe.';
//     $data = [
//         'fire_report_id' => $fireReport->id,  // Additional data for the notification
//     ];

//     // Send notification to all civilians
//     foreach ($civilianUsers as $user) {
//         if (!empty($user->fcm_token)) {
//             $this->firebaseService->sendNotification($user->fcm_token, $title, $body, $data);
//         }
//     }
// }

    public function quickReport(Request $request)
    {
        $validatedData = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location' => 'nullable|string',

        ]);

        $fireReport = new FireReports();
        if (Auth::check()) {
            $fireReport->reported_by = Auth::id(); // Authenticated user
        } else {
            $fireReport->contact_info = $request->contact_info; // Guest user
        }

        $fireReport->latitude = $request->latitude;
        $fireReport->longitude = $request->longitude;
        $fireReport->location = $request->location ?? 'Unknown Location';
        
        $fireReport->status = 'Pending';
        $fireReport->save();

        return response()->json(['message' => 'Emergency report submitted successfully!']);
    }

    public function getUserReports($reported_by)
    {
        $user = Auth::user(); // Get the currently authenticated user

        if ($user->id != $reported_by) {
            return response()->json(['message' => 'Unauthorized'], 403); // Unauthorized access
        }

        // Fetch the fire reports where the reportedBy (user_id) matches the current user
        $reports = FireReports::where('reported_by', $user->id)->get();

        // Return the reports as JSON
        return response()->json($reports);
    }

    public function store(Request $request) {
        $fields = $request->validate([
            'location' => 'required',
            'landmark' => 'nullable|max:255',
            'description' => 'nullable',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'contact_info' => 'nullable|string|max:255',
            'reported_by' => 'nullable|exists:users,id',
            'fire_report_image' => 'nullable|string|max:255'
        ]);

        // Ensure latitude & longitude are set
        $latitude = $fields['latitude'] ?? null;
        $longitude = $fields['longitude'] ?? null;

        // If no coordinates are provided, fetch them from the address
        if (!$latitude || !$longitude) {
            $coordinates = $this->getCoordinatesFromAddress($fields['location']);
            if ($coordinates) {
                $latitude = $coordinates['latitude'];
                $longitude = $coordinates['longitude'];
            } else {
                return response()->json(['error' => 'Unable to retrieve coordinates.'], 400);
            }
        }

        // Find nearest fire station
        $nearestFireStation = $this->findNearestFireStation($latitude, $longitude);
        if (!$nearestFireStation) {
            return response()->json(['error' => 'No nearby fire station found'], 400);
        }

        // Find nearest Barangay using the provided method
        $barangay_id = $this->findNearestBarangay($latitude, $longitude);

        if ($barangay_id) {
            // Assign the nearest Barangay ID to the report
            $fields['barangay_id'] = $barangay_id;
        } else {
            return response()->json(['error' => 'No nearby Barangay found'], 400);
        }

        // Assign coordinates & fire station
        $fields['latitude'] = $latitude;
        $fields['longitude'] = $longitude;
        $fields['fireStationId'] = $nearestFireStation->id;

        // Authenticated or guest reporting
        if ($request->user()) {
            $fields['reported_by'] = $request->user()->id;
            $fireReport = $request->user()->fireReports()->create($fields);
        } else {
            $fireReport = FireReports::create($fields);
        }
        $this->sendFireReportNotificationToCivilians($fireReport);


        return response()->json($fireReport, 201);
    }



    public function update(Request $request, $id)
    {
        $fireReport = FireReports::find($id);
        if (!$fireReport) {
            return response()->json(['message' => 'Fire report not found'], 404);
        }

        Gate::authorize('update', $fireReport);

        $fields = $request->validate([
            'reported_by' => 'required|exists:users,id',
            'location' => 'required',
            'landmark' => 'required|max:255',
            'description' => 'nullable',
            'status' => 'required|in:Ongoing,Resolved',
        ]);

        $coordinates = $this->getCoordinatesFromAddress($fields['location']);
        if (!$coordinates) {
            return response()->json(['error' => 'Unable to retrieve coordinates.'], 400);
        }

        $nearestFireStation = $this->findNearestFireStation($coordinates['latitude'], $coordinates['longitude']);
        if (!$nearestFireStation) {
            return response()->json(['error' => 'No fire stations found nearby.'], 400);
        }

        $fireReport->update([
            'reported_by' => $fields['reported_by'],
            'location' => $fields['location'],
            'landmark' => $fields['landmark'],
            'description' => $fields['description'],
            'status' => $fields['status'],
            'latitude' => $coordinates['latitude'],
            'longitude' => $coordinates['longitude'],
            'fireStationId' => $nearestFireStation->id,
        ]);

        return response()->json($fireReport, 200);
    }

    public function destroy($id)
    {
        $fireReport = FireReports::find($id);
        if (!$fireReport) {
            return response()->json(['message' => 'Fire report not found'], 404);
        }

        $fireReport->delete();

        return response()->json(['message' => 'Successfully deleted']);
    }

    // public function markAsContained($fireReportId){
    //     $user = Auth::user();

    //     $firefighter = Firefighter::where('userId', $user->id)->first();

    //     // Check if firefighter exists
    //     if (!$firefighter) {
    //         return response()->json([
    //             'message' => 'No firefighter found for this user.',
    //         ], 404);
    //     }

    //     $fireReport = FireReports::findOrFail($fireReportId);
        
    //     // Find all teams assigned to this fire report
    //     $teams = Team::where('assignedFireIncident', $fireReportId)->get();

    //     // Check if the firefighter is part of any of the teams or is a dispatcher
    //     $authorized = $teams->contains(function ($team) use ($firefighter) {
    //         // Check if the firefighter is part of the team
    //         return $team->firefighters->contains(function ($firefighterRecord) use ($firefighter) {
    //             return $firefighterRecord->id == $firefighter->id;
    //         }) || $team->firefighters->contains(function ($firefighterRecord) {
    //             return $firefighterRecord->position->position_name == 'Radio Operator'; // Check if the firefighter has 'Radio Operator' role
    //         });
    //     });

    //     // If the firefighter is not part of any team or is not a radio operator, return unauthorized
    //     if (!$authorized) {
    //         return response()->json([
    //             'message' => 'You are not authorized to mark this fire report as contained.',
    //         ], 403);
    //     }
        
    //     // Update the fire report status to "Resolved"
    //     $fireReport->status = 'Resolved';
    //     $fireReport->save();

    //     // Update the status and assignedFireIncident of all teams
    //     foreach ($teams as $team) {
    //         // Update the status to "Standby"
    //         $team->status = 'Standby';
    //         $team->assignedFireIncident = null;
    //         $team->save();
    //     }

    //     return response()->json([
    //         'message' => 'Fire report marked as contained successfully!',
    //         'status' => 'success'
    //     ]);
    // }

    public function getOngoingIncidents() {

        $incidents = FireReports::whereIn('status', ['Pending', 'Responding'])
                               ->select('id', 'location', 'status')
                               ->get();

        return response()->json($incidents);
    }
    
}
