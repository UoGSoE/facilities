<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoomTest extends TestCase
{
    use RefreshDatabase;

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
}
