<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CityAndMunicipality;

class CityAndMunicipalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Dasmariñas', 'province' => 'Cavite', 'type' => 'City'],
            ['name' => 'Tagaytay', 'province' => 'Cavite', 'type' => 'City'],
            ['name' => 'Imus', 'province' => 'Cavite', 'type' => 'City'],
            ['name' => 'Bacoor', 'province' => 'Cavite', 'type' => 'City'],
            ['name' => 'Cavite City', 'province' => 'Cavite', 'type' => 'City'],
            ['name' => 'General Trias', 'province' => 'Cavite', 'type' => 'City'],
            ['name' => 'Trece Martires', 'province' => 'Cavite', 'type' => 'City'],
            ['name' => 'Carmona', 'province' => 'Cavite', 'type' => 'City'],

            ['name' => 'Kawit', 'province' => 'Cavite', 'type' => 'Municipality'],
            ['name' => 'Noveleta', 'province' => 'Cavite', 'type' => 'Municipality'],
            ['name' => 'Rosario', 'province' => 'Cavite', 'type' => 'Municipality'],
            ['name' => 'Amadeo', 'province' => 'Cavite', 'type' => 'Municipality'],
            ['name' => 'Indang', 'province' => 'Cavite', 'type' => 'Municipality'],
            ['name' => 'Mendez', 'province' => 'Cavite', 'type' => 'Municipality'],
            ['name' => 'Alfonso', 'province' => 'Cavite', 'type' => 'Municipality'],
            ['name' => 'Silang', 'province' => 'Cavite', 'type' => 'Municipality'],
            ['name' => 'General Emilio Aguinaldo', 'province' => 'Cavite', 'type' => 'Municipality'],
            ['name' => 'General Mariano Alvarez', 'province' => 'Cavite', 'type' => 'Municipality'],
            ['name' => 'Magallanes', 'province' => 'Cavite', 'type' => 'Municipality'],
            ['name' => 'Maragondon', 'province' => 'Cavite', 'type' => 'Municipality'],
            ['name' => 'Naic', 'province' => 'Cavite', 'type' => 'Municipality'],
            ['name' => 'Tanza', 'province' => 'Cavite', 'type' => 'Municipality'],
            ['name' => 'Ternate', 'province' => 'Cavite', 'type' => 'Municipality'],
        ];

        foreach ($data as $item) {
            CityAndMunicipality::create($item);
        }
    }
    
}
