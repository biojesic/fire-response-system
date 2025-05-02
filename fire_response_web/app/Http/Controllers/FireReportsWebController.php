<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FireReports;
use App\Models\FireStation;
use App\Models\Team;
use App\Models\User;
use App\Models\Firefighter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class FireReportsWebController extends Controller
{ 
    public function index(Request $request)
    {
        $admin = auth()->user();
        $adminFirefighter = $admin->firefighter;
    
        if (!$adminFirefighter || !$adminFirefighter->fireStationId) {
            return redirect()->route('admin.dashboard')->withErrors('No fire station assigned.');
        }
    
        // Fetch reports associated with the fire station of the logged-in user
        $fireReports = FireReports::where('fireStationId', $adminFirefighter->fireStationId)
                                 ->latest() // Sort by the latest report
                                 ->paginate(10);
    
        return view('admin_pages.fire_reports', compact('fireReports'));
    }

    public function show($id){
        
    $report = FireReports::findOrFail($id);
    return view('admin_pages.fire_report_details', compact('report'));

    }

    

}
