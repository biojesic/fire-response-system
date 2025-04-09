<?php

namespace App\Http\Controllers;

use App\Models\Firefighter;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate incoming request
        $fields = $request->validate([
            'userFirstName' => 'required|string|max:255',
            'userLastName' => 'required|string|max:255',
            'userEmail' => 'required|string|email|max:255|unique:users',
            'userContactNumber' => 'required|string|max:20',
            'userAddress' => 'required|string|max:100000',
            'userAge' => 'required|integer',
            'userPassword' => 'required|string|min:8|confirmed',
        ]);

        // Create a civilian user
        $user = User::create([
            'userFirstName' => $fields['userFirstName'],
            'userLastName' => $fields['userLastName'],
            'userEmail' => $fields['userEmail'],
            'userContactNumber' => $fields['userContactNumber'],
            'userAddress' => $fields['userAddress'],
            'userAge' => $fields['userAge'],
            'userPassword' => bcrypt($fields['userPassword']),
            'userRole' => 'civilian',
            'userStatus' => 'Active',
        ]);

        // Generate token
        $token = $user->createToken($user->userEmail)->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }


    public function login(Request $request)
    {
        $request->validate([
            'userEmail' => 'required|email|exists:users,userEmail',
            'userPassword' => 'required'
        ]);

        $user = User::where('userEmail', $request->userEmail)->first();
        if (!$user || !Hash::check($request->userPassword, $user->userPassword)) {
            return [
                'msg' => 'Credentials incorrect.'
            ];
        }

        $token = $user->createToken($request->userEmail)->plainTextToken;

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
