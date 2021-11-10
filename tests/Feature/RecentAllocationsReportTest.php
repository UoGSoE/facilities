<?php

namespace Tests\Feature;

use App\Models\Desk;
use Tests\TestCase;
use App\Models\User;
use App\Models\People;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecentAllocationsReportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_recent_allocations_report_page()
    {
        $user = User::factory()->create();
        $person = People::factory()->create();
        $desk = Desk::factory()->create();
        $desk->allocateTo($person);

        $response = $this->actingAs($user)->get(route('reports.recent'));

        $response->assertOk();
        $response->assertSee('Recently allocated assets');
        $response->assertSeeLivewire('recent-report');
        $response->assertSee($desk->name);
        $response->assertSee($desk->room->name);
        $response->assertSee($desk->room->building->name);
    }
}
