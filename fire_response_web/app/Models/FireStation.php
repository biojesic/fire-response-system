<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class FireStation extends Pivot
{
    use HasFactory;

    protected $table = 'fire_station';
    protected $fillable = ['firestationName', 'firestationLocation', 'latitude', 'longitude', 'firestationContactNumber'];

    public function teams() {
        return $this->hasMany(Team::class, 'fireStationId');
    }

    public function barangays() {
    return $this->hasMany(Barangay::class);
    }

}   
