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
        'rejected_at',
        'rejection_reason'
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

    public function isRejected(): bool
    {
        return $this->brgy_status === 'rejected';
    }

    public function canReapply(): bool
    {
        if (!$this->reapply_allowed || $this->brgy_status !== 'rejected') {
            return false;
        }

        if (!$this->rejected_at) {
            return false;
        }

        return now()->diffInDays($this->rejected_at) >= 3;
    }

    public function approver() {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector() {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function adminUser() {
        return $this->hasOneThrough(
            User::class,
            BarangayPendingAdmin::class,
            'barangay_id',
            'id',
            'id',
            'user_id'
        );
    }
}



