<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirefighterReports extends Model
{
    use HasFactory;

    protected $fillable = [
        'fireReportId', // Reference to fire_reports table
        'fireFighterId', // Reference to firefighters table
        'reportType', // Type of report (Investigation, Incident, Spot Investigation)
        'details', // Report details
        'attachment', // Optional attachment (PDF, images)
    ];

    // Relationship with FireReport model
    public function fireReport()
    {
        return $this->belongsTo(FireReports::class, 'fireReportId');
    }

    // Relationship with Firefighter model
    public function firefighter()
    {
        return $this->belongsTo(Firefighter::class, 'fireFighterId');
    }
}
