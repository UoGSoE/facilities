<?php

namespace Tests\Feature;

use App\Models\Building;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BuildingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_details_for_a_given_building()
    {
        $user = User::factory()->create();
        $building = Building::factory()->create();

        $response = $this->actingAs($user)->get(route('building.show', $building));

        $response->assertOk();
        $response->assertSee('Details for building');
        $response->assertSee($building->name);
    }

    /** @test */
    public function we_can_create_a_new_building()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('building.create'));

        $response->assertOk();
        $response->assertSee("Add a new building");

        $response = $this->actingAs($user)->post(route('building.store'), [
            'name' => 'Test Building',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('home'));
        $this->assertEquals('Test Building', Building::first()->name);
    }

    /** @test */
    public function new_building_names_must_be_unique()
    {
        $user = User::factory()->create();
        $building = Building::factory()->create();

        $response = $this->actingAs($user)->post(route('building.store'), [
            'name' => $building->name,
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertCount(1, Building::all());
    }
}
