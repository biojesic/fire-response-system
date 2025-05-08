<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirefighterReports extends Model
{
    use HasFactory;

    protected $fillable = [
        'fireReportId',
        'fireFighterId',
        'reportType',
        'details',
        'attachment',
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
