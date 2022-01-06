<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Desk;
use App\Models\User;
use App\Models\Locker;
use App\Models\People;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $bioEngPeople = People::factory()->count(3)->create(['usergroup' => 'bioeng']);
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
            // usergroup
            ->assertSee($bioEngPeople[0]->surname)
            ->assertSee($bioEngPeople[1]->surname)
            ->set('usergroup', 'bioeng')
            ->assertSee($bioEngPeople[0]->surname)
            ->assertSee($bioEngPeople[1]->surname)
            ->set('usergroup', 'lasersandstuff')
            ->assertDontSee($bioEngPeople[0]->surname)
            ->assertDontSee($bioEngPeople[1]->surname)
            ->set('usergroup', '')
            ->assertSee($bioEngPeople[0]->surname)
            ->assertSee($bioEngPeople[1]->surname)
            // supervisor
            ->assertSee('TODO')
            // building
            ->assertSee('TODO')
            // room
            ->assertSee('TODO');
        ;
    }

    /** @test */
    public function we_can_see_the_details_for_an_individual_person()
    {
        $user = User::factory()->create();
        $person = People::factory()->create();
        $desk = $person->desks()->save(Desk::factory()->make());
        $locker = $person->lockers()->save(Locker::factory()->make());

        $response = $this->actingAs($user)->get(route('people.show', $person));

        $response->assertOk();
        $response->assertSee($person->full_name);
        $response->assertSee($person->email);
        $response->assertSeeInOrder(["Desk {$person->desks()->first()->name}", $person->desks()->first()->room->building->name, $person->desks()->first()->room->name]);
        $response->assertSeeInOrder(["Locker {$person->lockers()->first()->name}", $person->lockers()->first()->room->building->name, $person->lockers()->first()->room->name]);
    }
}
