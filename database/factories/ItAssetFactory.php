<?php

namespace Database\Factories;

use App\Models\People;
use Illuminate\Support\Str;
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
            'name' => ucwords($this->faker->words(2, true)),
            'asset_number' => ucfirst($this->faker->randomLetter()) . $this->faker->numberBetween(10000, 99999),
            'people_id' => People::factory(),
        ];
    }
}
