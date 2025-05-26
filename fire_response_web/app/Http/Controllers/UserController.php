<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\User;
use App\Models\User as ModelsUser;

class UserController extends Controller
{
    public function update(Request $request, $id)
    {
        $user = ModelsUser::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update logic here, e.g.
        $user->update($request->all());

        return response()->json($user);
    }

    public function getMyFirefighterID(Request $request)
    {
        $user = $request->user()->load('firefighter');
    
        if (!$user->firefighter) {
            return response()->json(['message' => 'No firefighter record found'], 404);
        }
    
        return response()->json([
            'firefighter_id' => $user->firefighter->id,
        ]);
    }

    public function saveFcmToken(Request $request) {
        // Validate the incoming request
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        // Assuming the user is authenticated (if not, handle accordingly)
        $user = $request->user();  // Get authenticated user

        // Update the FCM token in the database
        $user->fcm_token = $request->input('fcm_token');
        $user->save();

        return response()->json(['message' => 'FCM token saved successfully']);
    }
    
}
