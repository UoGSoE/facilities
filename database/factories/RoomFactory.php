<?php

namespace Database\Factories;

use App\Models\Building;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->numberBetween(100, 750),
            'building_id' => Building::factory(),
        ];
    }
}
