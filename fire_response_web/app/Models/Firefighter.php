<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Firefighter extends Model
{
    protected $fillable = [
        'userId',
        'fireStationId',
        'teamId',
        'position_id',
        'personalEquipment',
    ];

    protected $casts = [
        'personalEquipment' => 'array', // Automatically casts personal_equipment as an array
    ];

    // Relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    // Relationship with the FireStation model
    public function fireStation()
    {
        return $this->belongsTo(FireStation::class, 'fireStationId');
    }

    // Relationship with the Team model
    public function team()
    {
        return $this->belongsTo(Team::class, 'teamId');
    }

    public function position()
    {
        return $this->belongsTo(FirefighterPosition::class, 'position_id');
    }
    
    public function equipment()
    {
        return $this->belongsToMany(PersonalEquipment::class, 'firefighter_personal_equipment', 'firefighter_id', 'equipment_id');
    }

    public function rank()
    {
        return $this->belongsTo(FirefighterRank::class, 'rank_id');
    }
}
