<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Desk;
use App\Models\Locker;
use App\Models\People;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReallocationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_reallocation_form()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $buildings = Building::factory()->count(2)->create();

        $response = $this->actingAs($user)->get(route('room.reallocate', $room));

        $response->assertOk();
        $response->assertSee("Reallocate everyone from");
        $buildings->each(fn ($building) => $response->assertSee($building->name));
    }

    /** @test */
    public function we_can_bulk_reallocate_users_to_other_rooms()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $originalRoom = Room::factory()->create();
        $originalPeople = People::factory()->count(10)->create();
        $originalDesks = Desk::factory()->count(10)->create(['room_id' => $originalRoom->id]);
        $originalLockers = Locker::factory()->count(10)->create(['room_id' => $originalRoom->id]);
        $originalPeople->each(function ($person) use ($originalDesks, $originalLockers) {
            $originalDesks->where('people_id', null)->first()->update(['people_id' => $person->id]);
            $originalLockers->where('people_id', null)->first()->update(['people_id' => $person->id]);
        });
        $buildings = Building::factory()->count(2)->create();
        $buildings->each(function ($building) {
            $rooms = Room::factory()->count(5)->create([
                'building_id' => $building->id,
            ]);
            $rooms->each(function ($room) {
                Desk::factory()->count(10)->create(['room_id' => $room->id]);
                Locker::factory()->count(10)->create(['room_id' => $room->id]);
            });
        });

        $newBuilding = $buildings->random();

        $response = $this->actingAs($user)->post(route('room.do_reallocate', $originalRoom), [
            'reallocate_to' => [
                $newBuilding->id => $newBuilding->id,
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $originalPeople->each(
            fn ($person) => $this->assertTrue(
                $person->desks()->get()->every(fn ($desk) => intval($desk->room->building_id) == intval($newBuilding->id))
            )
        );
        $originalPeople->each(
            fn ($person) => $this->assertTrue(
                $person->lockers()->get()->every(fn ($locker) => intval($locker->room->building_id) == intval($newBuilding->id))
            )
        );
        $this->assertTrue($originalDesks->every(fn ($desk) => $desk->fresh()->people_id == null));
        $this->assertTrue($originalLockers->every(fn ($locker) => $locker->fresh()->people_id == null));
    }
}
