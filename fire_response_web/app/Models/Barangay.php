<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    protected $fillable = [
        'barangay_name',
        'barangay_hall_address',
        'contact_number',
        'latitude',
        'longitude',
        'fire_station_id',
        'barangay_legitimacy_proof',
        'brgy_status',
        'lgu_id',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at'
    ];
    
    public function fireStation()
    {
        return $this->belongsTo(FireStation::class);
    }

    public function fireReports() {
        return $this->hasMany(FireReport::class);
    }

    public function lgu()
    {
        return $this->belongsTo(CityAndMunicipality::class);
    }
}



