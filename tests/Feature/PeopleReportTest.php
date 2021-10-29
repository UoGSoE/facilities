<?php

namespace Tests\Feature;

use App\Models\People;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class PeopleReportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_people_report_page()
    {
        $user = User::factory()->create();
        $people = People::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('reports.people'));

        $response->assertOk();
        $response->assertSee('People Report');
        $response->assertSeeLivewire('people-report');
        $response->assertSee($people[0]->surname);
    }

    /** @test */
    public function we_can_filter_the_report_in_various_ways()
    {
        $user = User::factory()->create();
        $phdPeople = People::factory()->pgr()->count(3)->create();
        $academicPeople = People::factory()->academic()->count(3)->create();
        $currentUser = People::factory()->create([
            'start_at' => now()->subWeeks(10),
            'end_at' => now()->addWeeks(10),
        ]);
        $leavingSoonUser = People::factory()->create([
            'start_at' => now()->subWeeks(10),
            'end_at' => now()->addWeeks(2),
        ]);

        Livewire::actingAs($user)->test('people-report')
            // searching
            ->assertSee($phdPeople[0]->surname)
            ->assertSee($phdPeople[1]->surname)
            ->set('search', $phdPeople[0]->surname)
            ->assertSee($phdPeople[0]->surname)
            ->assertDontSee($phdPeople[1]->surname)
            ->set('search', '')
            // leaving soon
            ->assertSee($currentUser->surname)
            ->assertSee($leavingSoonUser->surname)
            ->set('leavingWeeks', 3)
            ->assertDontSee($currentUser->surname)
            ->assertSee($leavingSoonUser->surname)
            ->set('leavingWeeks', '')
            // type
            ->assertSee($phdPeople[0]->surname)
            ->assertSee($phdPeople[1]->surname)
            ->assertSee($academicPeople[0]->surname)
            ->assertSee($academicPeople[1]->surname)
            ->set('peopleType', People::TYPE_PGR)
            ->assertSee($phdPeople[0]->surname)
            ->assertSee($phdPeople[1]->surname)
            ->assertDontSee($academicPeople[0]->surname)
            ->assertDontSee($academicPeople[1]->surname)
            ->set('peopleType', People::TYPE_ACADEMIC)
            ->assertDontSee($phdPeople[0]->surname)
            ->assertDontSee($phdPeople[1]->surname)
            ->assertSee($academicPeople[0]->surname)
            ->assertSee($academicPeople[1]->surname)
            ->set('peopleType', 'any')
            ->assertSee($phdPeople[0]->surname)
            ->assertSee($phdPeople[1]->surname)
            ->assertSee($academicPeople[0]->surname)
            ->assertSee($academicPeople[1]->surname)
            ;
    }
}
