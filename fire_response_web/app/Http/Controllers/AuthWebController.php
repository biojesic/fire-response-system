<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Firefighter;

class AuthWebController extends Controller
{
    public function login(Request $request)
{
    // Validate user credentials
    $credentials = $request->only('email', 'password', 'userRole=firefighter');

    // Attempt to log the user in
    if (Auth::attempt($credentials, $request->remember)) {
        // Get the authenticated user
        $user = Auth::user();

        // Check if the user is a firefighter (assuming a Firefighter model exists)
        $firefighter = Firefighter::where('userId', $user->id)->first();

        if (!$firefighter) {
            // If the user is not a firefighter, log them out and return an error
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'failed' => 'You are not authorized to access this page.',
            ]);
        }

        // Redirect to the intended page after successful login
        return redirect()->intended('/admin/dashboard');
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
