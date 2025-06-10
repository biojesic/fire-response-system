<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class CivilianController extends Controller
{
    public function getRejectedApplication(Request $request) {
        $email = $request->query('email');

        if (!$email) {
            return response()->json(['message' => 'Email is required'], 400);
        }

        $civilian = User::where('email', $email)
            ->where('userStatus', 'Rejected')
            ->first();

        if (!$civilian) {
            return response()->json(['message' => 'Rejected applicant not found'], 404);
        }

        return response()->json([
            'first_name' => $civilian->userFirstName,
            'last_name' => $civilian->userLastName,
            'address' => $civilian->userAddress,
            'email' => $civilian->email,
            'contact_number' => $civilian->userContactNumber,
            'birth_date' => $civilian->userBirthDate,
            'profile_image_url' => asset('storage/' . $civilian->profile_image),
            'id_image_url' => asset('storage/' . $civilian->id_image),
            'rejection_reason' => $civilian->rejection_reason,
        ]);
    }

    public function reapply(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'userFirstName' => 'required|string|max:255',
            'userLastName' => 'required|string|max:255',
            'userAddress' => 'required|string|max:10000',
            'userContactNumber' => 'required|string|max:20',
            'userBirthDate' => 'required|date',
            'password' => 'required|string|min:8|confirmed',
            'profile_image' => 'required|string',
            'id_image' => 'required|string',
        ]);

        $civilian = User::where('email', $request->email)
            ->where('userStatus', 'Rejected')
            ->first();

        if (!$civilian) {
            return response()->json(['message' => 'You are not eligible to reapply'], 403);
        }

        if (!$civilian->reapply_allowed) {
            return response()->json(['message' => 'Reapplication limit reached. You are not allowed to reapply again.'], 403);
        }

        $maxReapplyAttempts = 2;
        $newCount = $civilian->reapplication_count + 1;

        $civilian->update([
            'userFirstName' => $request->userFirstName,
            'userLastName' => $request->userLastName,
            'userAddress' => $request->userAddress,
            'userContactNumber' => $request->userContactNumber,
            'userBirthDate' => $request->userBirthDate,
            'profile_image' => $request->profile_image,
            'id_image' => $request->id_image,
            'password' => Hash::make($request->password),
            'userStatus' => 'Unverified',
            'rejection_reason' => null,
            'email_verified_at' => null,
            'reapplication_count' => $newCount,
            'reapply_allowed' => $newCount < $maxReapplyAttempts,
        ]);

        return response()->json(['message' => 'Reapplication submitted successfully']);
    }

}
