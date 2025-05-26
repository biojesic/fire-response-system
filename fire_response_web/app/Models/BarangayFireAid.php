<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BarangayFireAid extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'barangay_id',
        'status',
        'photo',
        'barangay_id_path',
        'barangay_certificate_path',
        'position',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }
}
