<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Desk;
use App\Models\Room;
use App\Models\User;
use App\Models\Locker;
use App\Models\People;
use Livewire\Livewire;
use App\Models\Building;
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

        $building = Building::factory()->create(['name' => 'pyramid']);
        $room = Room::factory()->create(['building_id' => $building->id]);
        $pyramidPeople = People::factory()->count(3)->create();
        $pyramidDesks = Desk::factory()->count(3)->create(['room_id' => $room->id]);
        foreach (range(0, 2) as $index) {
            $pyramidDesks[$index]->allocateTo($pyramidPeople[$index]);
        }

        $otherBuilding = Building::factory()->create(['name' => 'skyscraper']);
        $otherRoom = Room::factory()->create(['building_id' => $otherBuilding->id]);
        $skyScraperPeople = People::factory()->count(3)->create();
        $skyScraperLockers = Locker::factory()->count(3)->create(['room_id' => $otherRoom->id]);
        foreach (range(0, 2) as $index) {
            $skyScraperLockers[$index]->allocateTo($skyScraperPeople[$index]);
        }

        $phdPeople = People::factory()->pgr()->count(3)->create();
        $academicPeople = People::factory()->academic()->count(3)->create();
        $bioEngPeople = People::factory()->count(3)->create(['usergroup' => 'bioeng']);
        $peopleWithSupervisor = People::factory()->count(3)->create(['supervisor_id' => $academicPeople[1]->id]);

        $verySpecificUser = People::factory()->pgr()->create(['supervisor_id' => $academicPeople[2]->id, 'usergroup' => 'bioeng']);
        $specificRoom = Room::factory()->create(['building_id' => $otherBuilding->id]);
        $specificDesk = Desk::factory()->create(['room_id' => $specificRoom->id]);
        $specificDesk->allocateTo($verySpecificUser);

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
            ->assertSee($phdPeople[0]->email)
            ->assertSee($phdPeople[1]->email)
            ->set('search', $phdPeople[0]->email)
            ->assertSee($phdPeople[0]->email)
            ->assertDontSee($phdPeople[1]->email)
            ->set('search', '')
            // leaving soon
            ->assertSee($currentUser->email)
            ->assertSee($leavingSoonUser->email)
            ->set('leavingWeeks', 3)
            ->assertDontSee($currentUser->email)
            ->assertSee($leavingSoonUser->email)
            ->set('leavingWeeks', '')
            // type
            ->assertSee($phdPeople[0]->email)
            ->assertSee($phdPeople[1]->email)
            ->assertSee($academicPeople[0]->email)
            ->assertSee($academicPeople[1]->email)
            ->set('peopleType', People::TYPE_PGR)
            ->assertSee($phdPeople[0]->email)
            ->assertSee($phdPeople[1]->email)
            ->assertDontSee($academicPeople[0]->email)
            ->assertDontSee($academicPeople[1]->email)
            ->set('peopleType', People::TYPE_ACADEMIC)
            ->assertDontSee($phdPeople[0]->email)
            ->assertDontSee($phdPeople[1]->email)
            ->assertSee($academicPeople[0]->email)
            ->assertSee($academicPeople[1]->email)
            ->set('peopleType', 'any')
            ->assertSee($phdPeople[0]->email)
            ->assertSee($phdPeople[1]->email)
            ->assertSee($academicPeople[0]->email)
            ->assertSee($academicPeople[1]->email)
            // usergroup
            ->assertSee($bioEngPeople[0]->email)
            ->assertSee($bioEngPeople[1]->email)
            ->set('usergroup', 'bioeng')
            ->assertSee($bioEngPeople[0]->email)
            ->assertSee($bioEngPeople[1]->email)
            ->set('usergroup', 'lasersandstuff')
            ->assertDontSee($bioEngPeople[0]->email)
            ->assertDontSee($bioEngPeople[1]->email)
            ->set('usergroup', '')
            ->assertSee($bioEngPeople[0]->email)
            ->assertSee($bioEngPeople[1]->email)
            // supervisor
            ->set('supervisor', $academicPeople[1]->id)
            ->assertSee($peopleWithSupervisor[0]->email)
            ->assertSee($peopleWithSupervisor[1]->email)
            ->assertDontSee($bioEngPeople[0]->email)
            ->assertDontSee($bioEngPeople[1]->email)
            ->assertDontSee($phdPeople[0]->email)
            ->assertDontSee($phdPeople[1]->email)
            ->set('supervisor', '')
            ->assertSee($peopleWithSupervisor[0]->email)
            ->assertSee($peopleWithSupervisor[1]->email)
            ->assertSee($bioEngPeople[0]->email)
            ->assertSee($bioEngPeople[1]->email)
            ->assertSee($phdPeople[0]->email)
            ->assertSee($phdPeople[1]->email)
            // building
            ->set('building', $pyramidPeople[0]->desks[0]->room->building_id)
            ->assertSee($pyramidPeople[0]->email)
            ->assertSee($pyramidPeople[1]->email)
            ->assertSee($pyramidPeople[2]->email)
            ->assertDontSee($bioEngPeople[0]->email)
            ->assertDontSee($bioEngPeople[1]->email)
            ->assertDontSee($phdPeople[0]->email)
            ->assertDontSee($phdPeople[1]->email)
            ->set('building', $skyScraperPeople[0]->lockers[0]->room->building_id)
            ->assertSee($skyScraperPeople[0]->email)
            ->assertSee($skyScraperPeople[1]->email)
            ->assertSee($skyScraperPeople[2]->email)
            ->assertDontSee($pyramidPeople[0]->email)
            ->assertDontSee($pyramidPeople[1]->email)
            ->assertDontSee($pyramidPeople[2]->email)
            ->assertDontSee($bioEngPeople[0]->email)
            ->assertDontSee($bioEngPeople[1]->email)
            ->assertDontSee($phdPeople[0]->email)
            ->assertDontSee($phdPeople[1]->email)
            ->set('building', '')
            ->assertSee($pyramidPeople[0]->email)
            ->assertSee($pyramidPeople[1]->email)
            ->assertSee($pyramidPeople[2]->email)
            ->assertSee($bioEngPeople[0]->email)
            ->assertSee($bioEngPeople[1]->email)
            ->assertSee($phdPeople[0]->email)
            ->assertSee($phdPeople[1]->email)
            // room
            ->set('room', $skyScraperPeople[0]->lockers[0]->room_id)
            ->assertSee($skyScraperPeople[0]->email)
            ->assertSee($skyScraperPeople[1]->email)
            ->assertSee($skyScraperPeople[2]->email)
            ->assertDontSee($pyramidPeople[0]->email)
            ->assertDontSee($pyramidPeople[1]->email)
            ->assertDontSee($pyramidPeople[2]->email)
            ->assertDontSee($bioEngPeople[0]->email)
            ->assertDontSee($bioEngPeople[1]->email)
            ->assertDontSee($phdPeople[0]->email)
            ->assertDontSee($phdPeople[1]->email)
            ->set('room', $pyramidPeople[0]->desks[0]->room_id)
            ->assertDontSee($skyScraperPeople[0]->email)
            ->assertDontSee($skyScraperPeople[1]->email)
            ->assertDontSee($skyScraperPeople[2]->email)
            ->assertSee($pyramidPeople[0]->email)
            ->assertSee($pyramidPeople[1]->email)
            ->assertSee($pyramidPeople[2]->email)
            ->assertDontSee($bioEngPeople[0]->email)
            ->assertDontSee($bioEngPeople[1]->email)
            ->assertDontSee($phdPeople[0]->email)
            ->assertDontSee($phdPeople[1]->email)
            ->set('room', '')
            ->assertSee($skyScraperPeople[0]->email)
            ->assertSee($skyScraperPeople[1]->email)
            ->assertSee($skyScraperPeople[2]->email)
            ->assertSee($pyramidPeople[0]->email)
            ->assertSee($pyramidPeople[1]->email)
            ->assertSee($pyramidPeople[2]->email)
            ->assertSee($bioEngPeople[0]->email)
            ->assertSee($bioEngPeople[1]->email)
            ->assertSee($phdPeople[0]->email)
            ->assertSee($phdPeople[1]->email)
            // combo-filters
            ->assertSee($verySpecificUser->email)
            ->assertSee($skyScraperPeople[0]->email)
            ->set('peopleType', 'PGR')
            ->set('usergroup', 'bioeng')
            ->set('supervisor', $academicPeople[2]->id)
            ->set('building', $specificRoom->building->id)
            ->set('room', $specificRoom->id)
            ->assertSee($verySpecificUser->email)
            ->assertDontSee($skyScraperPeople[0]->email)
            ->assertSet('peopleCount', 1);
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
