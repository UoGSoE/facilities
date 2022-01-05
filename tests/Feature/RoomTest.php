<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Desk;
use App\Models\Room;
use App\Models\User;
use App\Models\Locker;
use App\Models\Building;
use App\Models\People;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        $this->withoutExceptionHandling();
        config(['filesystems.default' => 'local']);
        Storage::fake('local');
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $room->desks()->saveMany(Desk::factory()->times(3)->make());
        $room->lockers()->saveMany(Locker::factory()->times(3)->make());
        $desks = $room->desks()->get();
        $lockers = $room->lockers()->get();

        $response = $this->actingAs($user)->get(route('room.edit', $room));

        $response->assertOk();
        $response->assertSee($room->name);

        $deskAllocations = [];
        foreach ($desks as $desk) {
            $deskAllocations[$desk->id] = [
                'people_id' => People::factory()->create()->id,
                'avanti_ticket_id' => rand(50000, 999999),
            ];
        }
        $lockerAllocations = [];
        foreach ($lockers as $locker) {
            $lockerAllocations[$locker->id] = [
                'people_id' => People::factory()->create()->id,
                'avanti_ticket_id' => rand(50000, 999999),
            ];
        }

        $response = $this->actingAs($user)->post(route('room.update', $room), [
            'name' => 'New Room Name',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
            'desks' => $deskAllocations,
            'lockers' => $lockerAllocations,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        tap($room->fresh(), function ($room) {
            $this->assertEquals('New Room Name', $room->name);
            $this->assertNotNull($room->image_path);
            Storage::disk('local')->assertExists($room->image_path);
            $room->lockers->each(fn ($locker) => $this->assertNotNull($locker->avanti_ticket_id));
            $room->desks->each(fn ($desk) => $this->assertNotNull($desk->avanti_ticket_id));
            $room->lockers->each(fn ($locker) => $this->assertNotNull($locker->people_id));
            $room->desks->each(fn ($desk) => $this->assertNotNull($desk->people_id));
        });
    }

    /** @test */
    public function there_is_a_route_to_get_the_image_for_a_room()
    {
        config(['filesystems.default' => 'local']);
        Storage::fake('local');
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $room->storeImage(UploadedFile::fake()->image('avatar.jpg'));

        $response = $this->actingAs($user)->get(route('room.image', $room));

        $response->assertOk();
    }

    /** @test */
    public function editing_an_existing_room_must_have_a_unique_name_in_its_building()
    {
        $user = User::factory()->create();
        $building1 = Building::factory()->create();
        $existingRoom1 = Room::factory()->create(['name' => 'HELLO', 'building_id' => $building1->id]);
        $existingRoom3 = Room::factory()->create(['name' => 'STRANGER', 'building_id' => $building1->id]);
        $building2 = Building::factory()->create();
        $existingRoom2 = Room::factory()->create(['name' => 'THERE', 'building_id' => $building2->id]);

        $response = $this->actingAs($user)->post(route('room.update', $existingRoom1), [
            'name' => 'STRANGER',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertEquals('HELLO', $existingRoom1->fresh()->name);

        $response = $this->actingAs($user)->post(route('room.update', $existingRoom1), [
            'name' => 'HELLO',
        ]);

        $response->assertSessionHasNoErrors();
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
