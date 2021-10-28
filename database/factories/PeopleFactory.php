<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PeopleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username' => $this->faker->userName(),
            'email' => $this->faker->email(),
            'surname' => $this->faker->lastName(),
            'forenames' => $this->faker->firstName(),
            'start_date' => now()->subMonths(rand(1, 24)),
            'end_date' => now()->addDays(rand(0, 700)),
        ];
    }
}
