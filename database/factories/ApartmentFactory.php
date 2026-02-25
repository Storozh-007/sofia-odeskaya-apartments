<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ApartmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->numberBetween(5000, 50000), // $50 - $500
            'capacity' => $this->faker->numberBetween(1, 6),
            'status' => $this->faker->randomElement(['free', 'taken', 'cleaning']),
        ];
    }
}
