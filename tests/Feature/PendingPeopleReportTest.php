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
use App\Mail\PendingAllocationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ohffs\CountsDatabaseQueries\CountsDatabaseQueries;

class PendingPeopleReportTest extends TestCase
{
    use RefreshDatabase;
    use CountsDatabaseQueries;


    /** @test */
    public function we_can_see_the_main_pending_people_report_page()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $pendingPeople = People::factory()->pending()->count(3)->create(['start_at' => now()->addWeek()]);
        $allocatedPeople = People::factory()->count(3)->create();
        $allocatedPeople->each(fn ($person) => $person->desks()->save(Desk::factory()->create()));
        $this->countDatabaseQueries();

        $response = $this->actingAs($user)->get(route('reports.pending'));

        $response->assertOk();
        $response->assertSee('Pending People');
        $this->assertQueryCountEquals(6);
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
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        Desk::factory()->count(5)->create();
        Locker::factory()->count(5)->create();
        $pendingPerson1 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $pendingPerson2 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $pendingPerson3 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $allocatedPerson = People::factory()->create();
        $allocatedPerson->desks()->save(Desk::factory()->create());

        Livewire::actingAs($user)->test('pending-people-report')
            ->assertSee($pendingPerson1->full_name)
            ->assertSee($pendingPerson2->full_name)
            ->assertSee($pendingPerson3->full_name)
            ->assertDontSee($allocatedPerson->full_name)
            ->set('deskAllocations.' . $pendingPerson1->id, 1)
            ->set('deskAllocations.' . $pendingPerson2->id, 1)
            ->set('deskAllocations.' . $pendingPerson3->id, 0)
            ->set('lockerAllocations.' . $pendingPerson1->id, 0)
            ->set('lockerAllocations.' . $pendingPerson2->id, 1)
            ->set('lockerAllocations.' . $pendingPerson3->id, 0)
            ->call('allocate')
            ->assertDontSee($pendingPerson1->full_name)
            ->assertDontSee($pendingPerson2->full_name)
            ->assertSee($pendingPerson3->full_name)
            ;

        $this->assertCount(1, $pendingPerson1->fresh()->desks);
        $this->assertCount(1, $pendingPerson2->fresh()->desks);
        $this->assertCount(0, $pendingPerson3->fresh()->desks);
        $this->assertCount(0, $pendingPerson1->fresh()->lockers);
        $this->assertCount(1, $pendingPerson2->fresh()->lockers);
        $this->assertCount(0, $pendingPerson3->fresh()->lockers);
    }

    /** @test */
    public function we_can_allocate_people_with_desks_and_lockers_to_a_specific_building_or_room()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $building1 = Building::factory()->create();
        $building2 = Building::factory()->create();
        $room1 = Room::factory()->create(['building_id' => $building1->id]);
        $room2 = Room::factory()->create(['building_id' => $building2->id]);
        Desk::factory()->count(5)->create([
            'room_id' => $room1->id,
        ]);
        Locker::factory()->count(5)->create([
            'room_id' => $room1->id,
        ]);
        Desk::factory()->count(5)->create([
            'room_id' => $room2->id,
        ]);
        Locker::factory()->count(5)->create([
            'room_id' => $room2->id,
        ]);
        $pendingPerson1 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $pendingPerson2 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $pendingPerson3 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $allocatedPerson = People::factory()->create();
        $allocatedPerson->desks()->save(Desk::factory()->create());

        Livewire::actingAs($user)->test('pending-people-report')
            ->assertSee($pendingPerson1->full_name)
            ->assertSee($pendingPerson2->full_name)
            ->assertSee($pendingPerson3->full_name)
            ->assertDontSee($allocatedPerson->full_name)
            ->set('buildingId', $building1->id)
            ->set('roomId', $room1->id)
            ->set('deskAllocations.' . $pendingPerson1->id, 1)
            ->set('deskAllocations.' . $pendingPerson2->id, 1)
            ->set('lockerAllocations.' . $pendingPerson2->id, 1)
            ->call('allocate')
            ->assertDontSee($pendingPerson1->full_name)
            ->assertDontSee($pendingPerson2->full_name)
            ->set('buildingId', $building2->id)
            ->set('deskAllocations.' . $pendingPerson3->id, 1)
            ->set('lockerAllocations.' . $pendingPerson3->id, 1)
            ->call('allocate')
            ->assertDontSee($pendingPerson1->full_name)
            ->assertDontSee($pendingPerson2->full_name)
            ->assertDontSee($pendingPerson3->full_name)
            ;

        $this->assertCount(1, $pendingPerson1->fresh()->desks);
        $this->assertCount(1, $pendingPerson2->fresh()->desks);
        $this->assertCount(1, $pendingPerson3->fresh()->desks);
        $this->assertCount(0, $pendingPerson1->fresh()->lockers);
        $this->assertCount(1, $pendingPerson2->fresh()->lockers);
        $this->assertCount(1, $pendingPerson3->fresh()->lockers);
        $this->assertTrue($pendingPerson1->fresh()->desks->first()->room->is($room1));
        $this->assertTrue($pendingPerson2->fresh()->desks->first()->room->is($room1));
        $this->assertTrue($pendingPerson2->fresh()->lockers->first()->room->is($room1));
        $this->assertTrue($pendingPerson3->fresh()->desks->first()->room->is($room2));
        $this->assertTrue($pendingPerson3->fresh()->lockers->first()->room->is($room2));
    }

    /** @test */
    public function if_there_arent_enough_desks_or_lockers_available_we_dont_allocate_anything()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        Desk::factory()->count(2)->create();
        Locker::factory()->count(2)->create();
        $pendingPerson1 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $pendingPerson2 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $pendingPerson3 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $allocatedPerson = People::factory()->create();
        $allocatedPerson->desks()->save(Desk::factory()->create());

        Livewire::actingAs($user)->test('pending-people-report')
            ->assertSee($pendingPerson1->full_name)
            ->assertSee($pendingPerson2->full_name)
            ->assertSee($pendingPerson3->full_name)
            ->assertDontSee($allocatedPerson->full_name)
            ->set('deskAllocations.' . $pendingPerson1->id, 3)
            ->set('deskAllocations.' . $pendingPerson2->id, 3)
            ->set('deskAllocations.' . $pendingPerson3->id, 0)
            ->set('lockerAllocations.' . $pendingPerson1->id, 0)
            ->set('lockerAllocations.' . $pendingPerson2->id, 3)
            ->set('lockerAllocations.' . $pendingPerson3->id, 0)
            ->assertSee('Not enough desks. Not enough lockers.')
            ->call('allocate')
            ->assertSee($pendingPerson1->full_name)
            ->assertSee($pendingPerson2->full_name)
            ->assertSee($pendingPerson3->full_name)
            ;

        $this->assertCount(0, $pendingPerson1->fresh()->desks);
        $this->assertCount(0, $pendingPerson2->fresh()->desks);
        $this->assertCount(0, $pendingPerson3->fresh()->desks);
        $this->assertCount(0, $pendingPerson1->fresh()->lockers);
        $this->assertCount(0, $pendingPerson2->fresh()->lockers);
        $this->assertCount(0, $pendingPerson3->fresh()->lockers);
    }

    /** @test */
    public function we_can_optionally_add_an_avanti_ticket_id_to_allocations()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        Desk::factory()->count(5)->create();
        Locker::factory()->count(5)->create();
        $pendingPerson1 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $pendingPerson2 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $pendingPerson3 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $allocatedPerson = People::factory()->create();
        $allocatedPerson->desks()->save(Desk::factory()->create());

        Livewire::actingAs($user)->test('pending-people-report')
            ->assertSee($pendingPerson1->full_name)
            ->assertSee($pendingPerson2->full_name)
            ->assertSee($pendingPerson3->full_name)
            ->assertDontSee($allocatedPerson->full_name)
            ->set('deskAllocations.' . $pendingPerson1->id, 1)
            ->set('avantiIds.' . $pendingPerson1->id, '12345')
            ->set('deskAllocations.' . $pendingPerson2->id, 1)
            ->set('avantiIds.' . $pendingPerson2->id, '54321')
            ->set('deskAllocations.' . $pendingPerson3->id, 0)
            ->set('lockerAllocations.' . $pendingPerson1->id, 0)
            ->set('lockerAllocations.' . $pendingPerson2->id, 1)
            ->set('lockerAllocations.' . $pendingPerson3->id, 0)
            ->call('allocate')
            ->assertDontSee($pendingPerson1->full_name)
            ->assertDontSee($pendingPerson2->full_name)
            ->assertSee($pendingPerson3->full_name)
            ;

        $this->assertCount(1, $pendingPerson1->fresh()->desks);
        $this->assertCount(1, $pendingPerson2->fresh()->desks);
        $this->assertCount(0, $pendingPerson3->fresh()->desks);
        $this->assertCount(0, $pendingPerson1->fresh()->lockers);
        $this->assertCount(1, $pendingPerson2->fresh()->lockers);
        $this->assertCount(0, $pendingPerson3->fresh()->lockers);

        $this->assertEquals('12345', $pendingPerson1->fresh()->desks->first()->avanti_ticket_id);
        $this->assertEquals('54321', $pendingPerson2->fresh()->desks->first()->avanti_ticket_id);
        $this->assertEquals('54321', $pendingPerson2->fresh()->lockers->first()->avanti_ticket_id);
    }

    /** @test */
    public function when_we_change_the_filters_the_desk_and_locker_allocations_are_reset()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        Desk::factory()->count(5)->create();
        Locker::factory()->count(5)->create();
        $pendingPerson1 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $pendingPerson2 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $pendingPerson3 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $allocatedPerson = People::factory()->create();
        $allocatedPerson->desks()->save(Desk::factory()->create());

        Livewire::actingAs($user)->test('pending-people-report')
            ->assertSee($pendingPerson1->full_name)
            ->assertSee($pendingPerson2->full_name)
            ->assertSee($pendingPerson3->full_name)
            ->assertDontSee($allocatedPerson->full_name)
            ->set('deskAllocations.' . $pendingPerson1->id, 1)
            ->set('avantiIds.' . $pendingPerson1->id, '12345')
            ->set('deskAllocations.' . $pendingPerson2->id, 1)
            ->set('avantiIds.' . $pendingPerson2->id, '54321')
            ->set('deskAllocations.' . $pendingPerson3->id, 0)
            ->set('lockerAllocations.' . $pendingPerson1->id, 0)
            ->set('lockerAllocations.' . $pendingPerson2->id, 1)
            ->set('lockerAllocations.' . $pendingPerson3->id, 0)
            ->set('filterWeeks', 5)
            ->assertSet('deskAllocations.' . $pendingPerson1->id, 0)
            ->assertSet('deskAllocations.' . $pendingPerson2->id, 0)
            ->assertSet('deskAllocations.' . $pendingPerson3->id, 0)
            ->assertSet('lockerAllocations.' . $pendingPerson1->id, 0)
            ->assertSet('lockerAllocations.' . $pendingPerson2->id, 0)
            ->assertSet('lockerAllocations.' . $pendingPerson3->id, 0)
            ->assertSet('avantiIds.' . $pendingPerson1->id, null)
            ->assertSet('avantiIds.' . $pendingPerson2->id, null)
            ->assertSet('avantiIds.' . $pendingPerson3->id, null)
            ;
    }

    /** @test */
    public function when_allocations_happen_an_email_is_sent_to_the_person_who_triggered_it_with_details_of_the_allocations()
    {
        Mail::fake();
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        Desk::factory()->count(5)->create();
        Locker::factory()->count(5)->create();
        $pendingPerson1 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $pendingPerson2 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $pendingPerson3 = People::factory()->pending()->create(['start_at' => now()->addWeek()]);
        $allocatedPerson = People::factory()->create();
        $allocatedPerson->lockers()->save(Locker::factory()->create());

        Livewire::actingAs($user)->test('pending-people-report')
            ->assertSee($pendingPerson1->full_name)
            ->assertSee($pendingPerson2->full_name)
            ->assertSee($pendingPerson3->full_name)
            ->assertDontSee($allocatedPerson->full_name)
            ->set('deskAllocations.' . $pendingPerson1->id, 1)
            ->set('avantiIds.' . $pendingPerson1->id, '12345')
            ->set('deskAllocations.' . $pendingPerson2->id, 1)
            ->set('avantiIds.' . $pendingPerson2->id, '54321')
            ->set('deskAllocations.' . $pendingPerson3->id, 0)
            ->set('lockerAllocations.' . $pendingPerson1->id, 0)
            ->set('lockerAllocations.' . $pendingPerson2->id, 1)
            ->set('lockerAllocations.' . $pendingPerson3->id, 0)
            ->call('allocate')
            ->assertDontSee($pendingPerson1->full_name)
            ->assertDontSee($pendingPerson2->full_name)
            ->assertSee($pendingPerson3->full_name)
            ;

        $this->assertCount(1, $pendingPerson1->fresh()->desks);
        $this->assertCount(1, $pendingPerson2->fresh()->desks);
        $this->assertCount(0, $pendingPerson3->fresh()->desks);
        $this->assertCount(0, $pendingPerson1->fresh()->lockers);
        $this->assertCount(1, $pendingPerson2->fresh()->lockers);
        $this->assertCount(0, $pendingPerson3->fresh()->lockers);

        $this->assertEquals('12345', $pendingPerson1->fresh()->desks->first()->avanti_ticket_id);
        $this->assertEquals('54321', $pendingPerson2->fresh()->desks->first()->avanti_ticket_id);
        $this->assertEquals('54321', $pendingPerson2->fresh()->lockers->first()->avanti_ticket_id);

        Mail::assertQueued(PendingAllocationEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
