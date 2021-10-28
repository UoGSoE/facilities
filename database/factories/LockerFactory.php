<?php

namespace Database\Factories;

use App\Models\People;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class LockerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->numberBetween(1, 100),
            'room_id' => Room::factory(),
            'people_id' => null,
        ];
    }
}
