<?php

namespace App\Http\Controllers;

use App\Models\Firefighter;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftController extends Controller
{


    public function updateShift(Request $request, $id)
    {
        $firefighter = Firefighter::findOrFail($id);

        $firefighter->shift_start = $request->shift_start;
        $firefighter->shift_end = $request->shift_end;
        $firefighter->save();

        return response()->json(['message' => 'Shift updated successfully!']);
    }

    public function autoUpdateStatus()
    {
        $now = Carbon::now()->format('H:i:s');

        $firefighters = Firefighter::all();
        foreach ($firefighters as $firefighter) {
            if ($firefighter->shift_start && $firefighter->shift_end) {
                if ($now >= $firefighter->shift_start && $now <= $firefighter->shift_end) {
                    $firefighter->status = 'standby';
                } else {
                    $firefighter->status = 'off duty';
                }
                $firefighter->save();
            }
        }
    }
}
