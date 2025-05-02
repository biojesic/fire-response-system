<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FirefighterRank;

class FirefighterRankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FirefighterRank::create([
            'rank_name' => 'FO1',  // First Officer rank
        ]);
        
        FirefighterRank::create([
            'rank_name' => 'FO2',  // Second Officer rank
        ]);
        
        FirefighterRank::create([
            'rank_name' => 'FO3',  // Third Officer rank
        ]);
    }
}
