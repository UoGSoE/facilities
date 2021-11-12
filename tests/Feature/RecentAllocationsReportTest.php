<?php

namespace Tests\Feature;

use App\Http\Livewire\RecentReport;
use App\Models\Desk;
use Tests\TestCase;
use App\Models\User;
use App\Models\People;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

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

    /** @test */
    public function we_can_export_the_recent_allocations_as_a_csv_file()
    {
        $this->markTestSkipped('TODO - waiting to see how to test a file download from livewire - https://github.com/livewire/livewire/discussions/4205');
        $user = User::factory()->create();
        $person = People::factory()->create();
        $desk = Desk::factory()->create();
        $desk->allocateTo($person);

        Livewire::actingAs($user)
            ->test(RecentReport::class)
            ->call('exportCsv')
            ->assertPayloadSet('effects.download', 'foo.csv');
    }
}
