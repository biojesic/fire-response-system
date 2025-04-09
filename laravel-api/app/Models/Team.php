<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;

    protected $fillable = [
        'teamName',
        'fireStationId',
        'assignedFireIncident',
        'firetruckName',
        'truckEquipmentList',
        'status',
    ];

    protected $casts = [
        'truckEquipmentList' => 'array', // Automatically casts truckEquipmentList as an array
    ];

    public function fireStation()
    {
        return $this->belongsTo(FireStation::class, 'fireStationId');
    }

    public function fireIncident()
    {
        return $this->belongsTo(FireReports::class, 'assignedFireIncident');
    }
}
