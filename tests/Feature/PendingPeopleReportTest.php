<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\People;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PendingPeopleReportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_main_pending_people_report_page()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $pendingPeople = People::factory()->pending()->count(3)->create();
        $allocatedPeople = People::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('reports.pending'));

        $response->assertOk();
        $response->assertSee('Pending People');
        $pendingPeople->each(function ($pendingPeople) use ($response) {
            $response->assertSee($pendingPeople->full_name);
        });
        $allocatedPeople->each(function ($allocatedPeople) use ($response) {
            $response->assertDontSee($allocatedPeople->full_name);
        });
    }

    /** @test */
    public function we_can_allocate_people_with_desks_and_lockers()
    {
        $this->markTestSkipped('Write test');
    }
}
