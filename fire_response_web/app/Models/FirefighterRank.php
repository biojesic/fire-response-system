<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirefighterRank extends Model
{
    /** @use HasFactory<\Database\Factories\FirefighterRankFactory> */
    use HasFactory;

    
    // Define the table name if it's different
    protected $table = 'firefighter_ranks';

    // Define fillable properties to protect against mass-assignment vulnerabilities
    protected $fillable = [
        'rank_name',
    ];

    // Define any relationships (if any) with other models, for example, with Firefighter
    public function firefighters()
    {
        return $this->hasMany(Firefighter::class, 'rank_id');
    }

}
