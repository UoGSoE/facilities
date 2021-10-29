<?php

namespace Database\Factories;

use App\Models\People;
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
            'start_at' => now()->subMonths(rand(1, 24)),
            'end_at' => now()->addDays(rand(0, 700)),
            'type' => $this->getRandomType(),
        ];
    }

    public function getRandomType()
    {
        return collect([
            People::TYPE_ACADEMIC,
            People::TYPE_PGR,
        ])->random();
    }

    public function pgr()
    {
        return $this->state(function () {
            return [
                'type' => People::TYPE_PGR,
            ];
        });
    }

    public function academic()
    {
        return $this->state(function () {
            return [
                'type' => People::TYPE_ACADEMIC,
            ];
        });
    }
}
