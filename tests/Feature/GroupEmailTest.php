<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Desk;
use App\Models\Room;
use App\Models\User;
use App\Models\Locker;
use App\Models\People;
use App\Mail\FacilitiesEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GroupEmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_send_an_email_to_everyone_in_a_room()
    {
        Mail::fake();
        $user = User::factory()->create();
        $room = Room::factory()->create();
        $otherRoom = Room::factory()->create();
        $peopleInRoom = People::factory()->count(2)->create();
        $peopleInOtherRoom = People::factory()->count(2)->create();
        $peopleInRoom->each(function ($person) use ($room) {
            Desk::factory()->create([
                'people_id' => $person->id,
                'room_id' => $room->id,
            ]);
            Locker::factory()->create([
                'people_id' => $person->id,
                'room_id' => $room->id,
            ]);
        });
        $peopleInOtherRoom->each(function ($person) use ($otherRoom) {
            Desk::factory()->create([
                'people_id' => $person->id,
                'room_id' => $otherRoom->id,
            ]);
            Locker::factory()->create([
                'people_id' => $person->id,
                'room_id' => $otherRoom->id,
            ]);
        });

        $response = $this->actingAs($user)->from(route('room.show', $room))->post(route('email.room', $room), [
            'subject' => 'Test subject',
            'message' => 'Test message',
        ]);

        $response->assertRedirect(route('room.show', $room));
        Mail::assertQueued(FacilitiesEmail::class, 2);
        Mail::assertQueued(FacilitiesEmail::class, fn ($mail) => $mail->hasTo($peopleInRoom[0]->email) && $mail->subject == 'Test subject' && $mail->message == 'Test message');
        Mail::assertQueued(FacilitiesEmail::class, fn ($mail) => $mail->hasTo($peopleInRoom[1]->email) && $mail->subject == 'Test subject' && $mail->message == 'Test message');
    }

    /** @test */
    public function we_can_send_an_email_to_everyone_in_a_building()
    {
        $this->markTestSkipped('Not implemented yet');
    }

    /** @test */
    public function we_can_send_an_email_to_everyone_with_an_desk_or_locker_allocation()
    {
        $this->markTestSkipped('Not implemented yet');
    }

    /** @test */
    public function the_default_subject_on_the_email_form_is_appropriate()
    {
        $this->markTestSkipped('Not implemented yet');
    }
}
