<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FireReports extends Model
{
    /** @use HasFactory<\Database\Factories\FireReportsFactory> */
    use HasFactory;

    protected $fillable = [
        'reported_by',
        'location',
        'landmark',
        'description',
        'latitude',
        'longitude',
        'contact_info',
        'fireStationId',
        'marked_as_contained_by_id',
        'marked_as_contained_at',
    ];

    protected $casts = [
        'marked_as_contained_at' => 'datetime',
    ];
    

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fireReports()
    {
        return $this->hasMany(FireReports::class, 'reported_by');
    }

    public function fireStation()
    {
        return $this->belongsTo(FireStation::class, 'fireStationId');
    }

    public function assignedTeams() {
        return $this->hasMany(Team::class, 'assignedFireIncident');
    }

    public function stages() {
        return $this->belongsTo(RealTimeFireReport::class, 'fire_report_id');
    }

    public function markedBy() {
        return $this->belongsTo(Firefighter::class, 'marked_as_contained_by_id');
    }

    public function firefighterReports() {
        return $this->hasMany(FirefighterReports::class, 'fireReportId');
    }

    public function responders() {
        return $this->belongsToMany(Firefighter::class, 'firefighter_reports', 'fireReportId', 'fireFighterId')
        ->with('user:id,userFirstName,userLastName');
    }

}
