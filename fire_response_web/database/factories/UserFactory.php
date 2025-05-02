<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'userFirstName' => fake()->firstName(),
            'userLastName' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'userContactNumber' => fake()->phoneNumber(),
            'userAddress' => fake()->address(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'password' => static::$password ??= Hash::make('password'), // You can customize this password
            'userBirthDate' => fake()->date(),
            'userStatus' => 'active',
            'userRole' => 'civilian',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
