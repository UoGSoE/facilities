<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\People;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SupervisorReportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_supervisors_report()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $supervisor1 = People::factory()->create();
        $supervisor2 = People::factory()->create();
        $others1 = People::factory()->count(rand(1, 5))->create(['supervisor_id' => $supervisor1->id]);
        $others2 = People::factory()->count(rand(1, 5))->create(['supervisor_id' => $supervisor2->id]);

        $response = $this->actingAs($user)->get('/reports/supervisors');

        $response->assertOk();
        $response->assertSee($supervisor1->full_name);
        $response->assertSee($supervisor2->full_name);
    }

    /** @test */
    public function we_can_see_a_report_for_a_specific_supervisor()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $supervisor1 = People::factory()->create();
        $others1 = People::factory()->count(rand(1, 5))->create(['supervisor_id' => $supervisor1->id]);

        $response = $this->actingAs($user)->get(route('reports.supervisor', $supervisor1->id));

        $response->assertOk();
        $response->assertSee($supervisor1->full_name);
    }
}
