<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityAndMunicipality extends Model
{
    use HasFactory;

    protected $table = 'cities_and_municipalities';

    protected $fillable = [
        'name',
        'type',
        'province',
    ];

    public function barangays()
    {
        return $this->hasMany(Barangay::class, 'lgu_id');
    }

    public function fireStation()
    {
        return $this->hasOne(FireStation::class, 'city_municipality_id');
    }
}
