<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalEquipment extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Define the relationship to Firefighter
    public function firefighters()
    {
        return $this->belongsToMany(Firefighter::class, 'firefighter_personal_equipment', 'equipment_id', 'firefighter_id');
    }
}
