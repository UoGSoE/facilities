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
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->email(),
            'surname' => $this->faker->lastName(),
            'forenames' => $this->faker->firstName(),
            'start_at' => now()->subMonths(rand(1, 24)),
            'end_at' => now()->addDays(rand(0, 700)),
            'type' => $this->getRandomType(),
        ];
    }

    public function getRandomType()
    {
        return app('people.types')->random();
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

    public function pending()
    {
        return $this->state(function () {
            return [
                'start_at' => now()->addWeeks(rand(1, 8)),
                'end_at' => now()->addWeeks(rand(12, 100)),
            ];
        });
    }
}
