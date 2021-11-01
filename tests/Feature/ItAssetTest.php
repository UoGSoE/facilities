<?php

namespace Tests\Feature;

use App\Models\ItAsset;
use App\Models\People;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItAssetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_it_asset_report()
    {
        $user = User::factory()->create();
        $people = People::factory()->times(5)->create();
        $people->each(fn ($person) => $person->itAssets()->save(ItAsset::factory()->make()));

        $response = $this->actingAs($user)->get(route('reports.itassets'));

        $response->assertOk();
        $response->assertSee('IT Assets Report');
        $people->each(function ($person) use ($response) {
            $response->assertSee($person->full_name);
            $response->assertSee($person->email);
        });
    }
}
