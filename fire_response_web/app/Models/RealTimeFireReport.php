<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RealTimeFireReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'fire_report_id',
        'stage',
        'content',
    ];

    protected $casts = [
        'content' => 'array', // Automatically decode JSON to array
    ];

    public function fireReport() {
        return $this->belongsTo(FireReport::class, 'fire_report_id');
    }
}
