<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Firefighter;
use Illuminate\Support\Facades\DB;

class AuthWebController extends Controller
{
public function login(Request $request)
{
    // Validate user credentials (email and password only)
    $credentials = $request->only('email', 'password');
    
    // Attempt to log the user in
    if (Auth::attempt($credentials, $request->remember)) {
        // Get the authenticated user
        $user = Auth::user();

        // Log the user role for debugging
        \Log::info('Authenticated User: ' . $user->email);
        \Log::info('User Role: ' . $user->userRole);

        // Check if the user is a firefighter
        if ($user->userRole === 'firefighter') {
            \Log::info('User is a firefighter.');

            $firefighter = Firefighter::where('userId', $user->id)
                ->with('position')  // Eager load the position relationship
                ->first();

            if (!$firefighter) {
                \Log::error('Firefighter not found for user ID: ' . $user->id);
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'failed' => 'You are not authorized to access this page.',
                ]);
            }

            // Log the position_id and position name for debugging
            \Log::info('Firefighter Position ID: ' . $firefighter->position_id);
            \Log::info('Firefighter Position Name: ' . $firefighter->position->position_name);

            // Get the firefighter's position
            $position = $firefighter->position->position_name;

            // Check the firefighter's position and redirect accordingly
            if ($position === 'Admin') {
                \Log::info('Redirecting to Admin Dashboard');
                return redirect()->route('admin.dashboard');
            } elseif ($position === 'Super Admin') {
                \Log::info('Redirecting to Super Admin Dashboard');
                return redirect()->route('superadmin.dashboard');
            }
        }

        // Check if the user is a Barangay admin
        if ($user->userRole === 'Barangay') {
            \Log::info('User Role: Barangay');

            // Get the position from the barangay_fire_aids table
            $barangayAdmin = DB::table('barangay_fire_aids')
                                ->where('user_id', $user->id)
                                ->first();

            // Log position for debugging
            \Log::info('Barangay Admin Position: ' . ($barangayAdmin->position ?? 'No Position Found'));

            // Check if the user is an Admin in the barangay_fire_aids table
            if ($barangayAdmin && $barangayAdmin->position === 'Admin') {
                \Log::info('Redirecting to Barangay Dashboard');
                return redirect()->route('brgy.dashboard'); // Ensure 'brgy.dashboard' is the correct route name
            } else {
                \Log::info('User is not Admin in Barangay Fire Aids');
            }
        }

        // Default redirection for all other users
        return redirect()->intended('/');
    }

    // If authentication fails, return back with a custom error message
    return back()->withErrors([
        'failed' => 'Login failed. Please try again.',
    ]);
}

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

}
