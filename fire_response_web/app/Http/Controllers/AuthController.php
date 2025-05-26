<?php

namespace App\Http\Controllers;

// use App\Models\Firefighter;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
// use Illuminate\Support\Facades\Mail;


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
            'id_image' => 'required|image|mimes:jpeg,png,jpg|max:10000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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

        // Handle file uploads
        if ($request->hasFile('id_image')) {
            $idImagePath = $request->file('id_image')->store('user_images', 'public');
        } else {
            // Should never happen since id_image is required, but just in case
            $idImagePath = null;
        }

        $profileImagePath = null;
        if ($request->hasFile('profile_image')) {
            $profileImagePath = $request->file('profile_image')->store('user_images', 'public');
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
            'userStatus' => 'Unverified',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'id_image' => $idImagePath,
            'profile_image' => $profileImagePath,
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

        if ($user->userStatus === 'Rejected') {
        return response()->json([
            'message' => 'Your registration was rejected.',
            'rejection_reason' => $user->rejection_reason,
            'can_reapply' => $user->reapply_allowed,
        ], 403);
        }

        if ($user->userStatus === 'Inactive') {
            return response()->json([
                'message' => 'Your account is currently inactive. Contact support.'
            ], 403);
        }

        if ($user->userStatus === 'Unverified') {
        return response()->json([
            'message' => 'Your account is still under verification.'
        ], 403);
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

    // Forgot password functionality
    // public function sendPasswordResetLink(Request $request) {
    //     // Validate email
    //     $request->validate([
    //         'email' => 'required|email|exists:users,email',
    //     ]);

    //     // Attempt to send the password reset link to the provided email address
    //     $status = Password::sendResetLink($request->only('email'));

    //     // Return response based on whether the email was sent successfully
    //     return $status == Password::RESET_LINK_SENT
    //         ? response()->json(['message' => 'We have emailed your password reset link!'], 200)
    //         : response()->json(['message' => 'Failed to send reset link. Please check your email and try again.'], 400);
    // }

    // public function resetPassword(Request $request) {
    //     // Validate the inputs
    //     $request->validate([
    //         'token' => 'required',
    //         'email' => 'required|email',
    //         'password' => 'required|confirmed|min:8',
    //     ]);

    //     // Attempt to reset the password using the token
    //     $status = Password::reset(
    //         $request->only('email', 'password', 'password_confirmation', 'token'),
    //         function ($user) use ($request) {
    //             // Update the user's password
    //             $user->password = Hash::make($request->password);
    //             $user->save();
    //         }
    //     );

    //     // Return response based on status
    //     return $status == Password::PASSWORD_RESET
    //         ? response()->json(['message' => 'Password has been reset successfully!'], 200)
    //         : response()->json(['message' => 'Failed to reset password. Please try again.'], 400);
    // }


}
