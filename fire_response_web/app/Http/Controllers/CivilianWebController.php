<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class CivilianWebController extends Controller
{
   public function index() {
        $civilians = User::where('userRole', 'civilian')->paginate(10);
        return view('superadmin_pages.civilians', compact('civilians'));
    }

    public function show($id) {
        $civilian = User::findOrFail($id);
        return view('superadmin_pages.civilian_details', compact('civilian'));
    }

    public function updateStatus(Request $request, $id) {
        // $civilian = User::findOrFail($id);
        // $civilian->status = $request->input('status');
        // $civilian->save();

        // return redirect()->route('civilians.index')->with('status', 'Civilian status updated successfully');
    }

    public function civilianVerificationPage() {
        $unverifiedCivilians = User::where('userStatus', 'Unverified')
            ->where('userRole', 'Civilian')
            ->get();
            
        return view('superadmin_pages.civilian_verification', compact('unverifiedCivilians'));
    }

    public function showCivilianVerificationDetails($id) {
        $civilianUser = User::find($id);

        if (!$civilianUser) {
            return redirect()->route('civilians.index')->with('error', 'User not found.');
        }

        return view('superadmin_pages.civilian_verification_details', compact('civilianUser'));
    }

    public function approveCivilian($userId) {
        $user = User::findOrFail($userId);

        $user->update([
            'userStatus' => 'Active',
            'rejection_reason' => null,
            'reapply_allowed' => false,
        ]);

        return redirect()->route('civilians.verificationpage')->with('message', 'User verified successfully.');
    }

    public function rejectCivilian(Request $request, $userId) {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($userId);

        $user->update([
            'userStatus' => 'Rejected',
            'rejection_reason' => $request->rejection_reason,
            'reapply_allowed' => true,
            'last_rejection_at' => now(),
        ]);

        return redirect()->route('civilians.verificationpage')->with('message', 'Application Rejected.');
    }
}
