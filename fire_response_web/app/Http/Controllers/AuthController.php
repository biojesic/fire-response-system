<?php

namespace App\Http\Controllers;

// use App\Models\Firefighter;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;



class AuthController extends Controller
{


    public function register(Request $request)
    {
        // Validate incoming request
        $fields = $request->validate([
            'userFirstName' => 'required|string|max:255',
            'userLastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'userContactNumber' => 'nullable|string|max:20',
            'userAddress' => 'required|string|max:100000',
            'userBirthDate' => 'nullable|date',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Initialize coordinates
        $latitude = null;
        $longitude = null;

        try {
            // $apiKey = config('services.google_maps.key');
            $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
                'address' => $fields['userAddress'],
                'key' => env('GOOGLE_MAPS_API_KEY'),
            ]);

            if ($response->ok() && isset($response['results'][0])) {
                $location = $response['results'][0]['geometry']['location'];
                $latitude = $location['lat'];
                $longitude = $location['lng'];
            }
        } catch (\Exception $e) {
            // Optional: Log error if needed
            Log::warning('Geocoding failed: ' . $e->getMessage());
        }

        // Create a civilian user
        $user = User::create([
            'userFirstName' => $fields['userFirstName'],
            'userLastName' => $fields['userLastName'],
            'email' => $fields['email'],
            'userContactNumber' => $fields['userContactNumber'],
            'userAddress' => $fields['userAddress'],
            'userBirthDate' => $fields['userBirthDate'],
            'password' => bcrypt($fields['password']),
            'userRole' => 'civilian',
            'userStatus' => 'Active',
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        // Generate token
        $token = $user->createToken($user->email)->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                'msg' => 'Credentials incorrect.'
            ];
        }

        $token = $user->createToken($request->email)->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return [
            'msg' => 'Logged out successfully.'
        ];
    }
}
