<?php

namespace Tests\Feature;

use App\Models\Building;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BuildingReportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_building_report_page()
    {
        $user = User::factory()->create();
        $building1 = Building::factory()->create(['name' => 'First Building']);
        $building2 = Building::factory()->create(['name' => 'Second Building']);

        $response = $this->actingAs($user)->get('/reports/buildings');

        $response->assertOk();
        $response->assertSee('First Building');
        $response->assertSee('Second Building');
    }
}
