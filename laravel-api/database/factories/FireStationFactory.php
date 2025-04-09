<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FireStation;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FireStation>
 */
class FireStationFactory extends Factory
{
    protected $model = FireStation::class;

    public function definition(): array
    {
        return [
            'firestationName' => $this->faker->company,
            'firestationLocation' => $this->faker->address,
            'firestationContactNumber' => $this->faker->phoneNumber,
        ];
    }
}
