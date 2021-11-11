<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->numberBetween(1, 30),
            'room_id' => Room::factory(),
            'people_id' => null,
            'avanti_ticket_id' => null,
        ];
    }
}
