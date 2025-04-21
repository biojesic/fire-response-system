<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthWebController extends Controller
{
    public function login(Request $request)
    {
        // Validate user credentials
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->remember)) {
            // Redirect to intended page after successful login
            return redirect()->intended('/admin/dashboard');
        }

        // If authentication fails, return back with custom error messages
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
