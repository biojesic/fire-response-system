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
        'fireStationId'
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
}
