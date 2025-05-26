<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    protected $fillable = [
        'barangay_name',
        'address',
        'contact_number',
        'latitude',
        'longitude',
        'fire_station_id',
        'barangay_legitimacy_proof',
    ];
    
    public function fireStation()
    {
        return $this->belongsTo(FireStation::class);
    }

    public function fireReports() {
        return $this->hasMany(FireReport::class);
    }
}



