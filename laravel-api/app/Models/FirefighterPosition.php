<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirefighterPosition extends Model
{
    use HasFactory;

    protected $fillable = ['position_name'];

    public function firefighters()
    {
        return $this->hasMany(Firefighter::class, 'position_id');
    }
}
