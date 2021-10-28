<?php

namespace Database\Factories;

use App\Models\People;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItAssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'asset_number' => $this->faker->numberBetween(1000, 9999),
            'people_id' => People::factory(),
        ];
    }
}
