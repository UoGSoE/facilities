<?php

namespace Tests\Feature;

use App\Models\Building;
use Tests\TestCase;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoomTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_create_a_new_room()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $building = Building::factory()->create();

        $response = $this->actingAs($user)->get(route('room.create', $building->id));

        $response->assertOk();
        $response->assertSeeInOrder(["Add a new room to", $building->name]);

        $response = $this->actingAs($user)->post(route('room.store', $building->id), [
            'name' => 'Room 101',
            'desks' => '10',
            'lockers' => '5',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('building.show', $building->id));
        tap($building->rooms()->first(), function ($room) {
            $this->assertEquals('Room 101', $room->name);
            $this->assertEquals(10, $room->desks()->count());
            $this->assertEquals(5, $room->lockers()->count());
        });
    }

    /** @test */
    public function a_new_rooms_name_must_be_unique_in_its_building()
    {
        $user = User::factory()->create();
        $building1 = Building::factory()->create();
        $existingRoom1 = Room::factory()->create(['name' => 'HELLO', 'building_id' => $building1->id]);
        $building2 = Building::factory()->create();
        $existingRoom2 = Room::factory()->create(['name' => 'THERE', 'building_id' => $building2->id]);

        $response = $this->actingAs($user)->post(route('room.store', $building1->id), [
            'name' => 'HELLO',
            'desks' => '10',
            'lockers' => '5',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertEquals(2, Room::count());

        $response = $this->actingAs($user)->post(route('room.store', $building2->id), [
            'name' => 'HELLO',
            'desks' => '10',
            'lockers' => '5',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals(3, Room::count());
    }

    /** @test */
    public function we_can_edit_an_existing_room()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create();

        $response = $this->actingAs($user)->get(route('room.edit', $room));

        $response->assertOk();
        $response->assertSee($room->name);

        $response = $this->actingAs($user)->post(route('room.update', $room), [
            'name' => 'New Room Name',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals('New Room Name', $room->fresh()->name);
    }

    /** @test */
    public function editing_an_existing_room_must_have_a_unique_name_in_its_building()
    {
        $user = User::factory()->create();
        $building1 = Building::factory()->create();
        $existingRoom1 = Room::factory()->create(['name' => 'HELLO', 'building_id' => $building1->id]);
        $building2 = Building::factory()->create();
        $existingRoom2 = Room::factory()->create(['name' => 'THERE', 'building_id' => $building2->id]);

        $response = $this->actingAs($user)->post(route('room.update', $existingRoom1), [
            'name' => 'HELLO',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertEquals('HELLO', $existingRoom1->fresh()->name);

        $response = $this->actingAs($user)->post(route('room.update', $existingRoom1), [
            'name' => 'THERE',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals('THERE', $existingRoom1->fresh()->name);
    }

    /** @test */
    public function we_can_delete_an_existing_room_via_a_confirmation_page()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create(['name' => 'ROOM TO BE DELETED']);

        $response = $this->actingAs($user)->get(route('building.show', $room->building));

        $response->assertSee('ROOM TO BE DELETED');

        $response = $this->actingAs($user)->get(route('room.delete', $room));

        $response->assertOk();
        $response->assertSee("Really delete room $room->name in {$room->building->name}?");

        $response = $this->actingAs($user)->post(route('room.destroy', $room));

        $response->assertRedirect(route('building.show', $room->building->id));
        $response->assertDontSee('ROOM TO BE DELETED');
        $this->assertEquals(0, Room::count());
    }
}
