<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Desk;
use App\Models\Room;
use App\Models\User;
use App\Models\Locker;
use App\Models\People;
use App\Mail\FacilitiesEmail;
use App\Models\Building;
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

        $response = $this->actingAs($user)->get(route('email.room_form', $room));

        $response->assertOk();
        $response->assertSeeInOrder(['Send email to people in', $room->building->name, 'room', $room->name]);

        $response = $this->actingAs($user)->post(route('email.room', $room), [
            'subject' => 'Test subject',
            'message' => 'Test message',
        ]);

        $response->assertRedirect(route('room.show', $room));
        Mail::assertQueued(FacilitiesEmail::class, 3);
        Mail::assertQueued(FacilitiesEmail::class, fn ($mail) => $mail->hasTo($peopleInRoom[0]->email) && $mail->subject == 'Test subject' && $mail->message == 'Test message');
        Mail::assertQueued(FacilitiesEmail::class, fn ($mail) => $mail->hasTo($peopleInRoom[1]->email) && $mail->subject == 'Test subject' && $mail->message == 'Test message');
        // we also send a copy to the user who triggered the email
        Mail::assertQueued(FacilitiesEmail::class, fn ($mail) => $mail->hasTo($user->email) && $mail->subject == 'Test subject' && $mail->message == 'Test message');
    }

    /** @test */
    public function we_can_send_an_email_to_everyone_in_a_building()
    {
        Mail::fake();
        $user = User::factory()->create();
        $building1 = Building::factory()->create();
        $building2 = Building::factory()->create();
        $room1 = Room::factory()->create(['building_id' => $building1->id]);
        $room2 = Room::factory()->create(['building_id' => $building1->id]);
        $otherBuildingRoom = Room::factory()->create(['building_id' => $building2->id]);
        $peopleInRoom = People::factory()->count(2)->create();
        $peopleInOtherRoom = People::factory()->count(2)->create();
        $peopleInOtherBuilding = People::factory()->count(2)->create();
        $peopleInRoom->each(function ($person) use ($room1) {
            Desk::factory()->create([
                'people_id' => $person->id,
                'room_id' => $room1->id,
            ]);
            Locker::factory()->create([
                'people_id' => $person->id,
                'room_id' => $room1->id,
            ]);
        });
        $peopleInOtherRoom->each(function ($person) use ($room2) {
            Desk::factory()->create([
                'people_id' => $person->id,
                'room_id' => $room2->id,
            ]);
            Locker::factory()->create([
                'people_id' => $person->id,
                'room_id' => $room2->id,
            ]);
        });
        $peopleInOtherBuilding->each(function ($person) use ($otherBuildingRoom) {
            Desk::factory()->create([
                'people_id' => $person->id,
                'room_id' => $otherBuildingRoom->id,
            ]);
            Locker::factory()->create([
                'people_id' => $person->id,
                'room_id' => $otherBuildingRoom->id,
            ]);
        });

        $response = $this->actingAs($user)->get(route('email.building_form', $room1->building));

        $response->assertOk();
        $response->assertSeeInOrder(['Send email to people in', $room1->building->name]);

        $response = $this->actingAs($user)->post(route('email.building', $room1->building), [
            'subject' => 'Test subject',
            'message' => 'Test message',
        ]);

        $response->assertRedirect(route('building.show', $room1->building));
        Mail::assertQueued(FacilitiesEmail::class, 5);
        Mail::assertQueued(FacilitiesEmail::class, fn ($mail) => $mail->hasTo($peopleInRoom[0]->email) && $mail->subject == 'Test subject' && $mail->message == 'Test message');
        Mail::assertQueued(FacilitiesEmail::class, fn ($mail) => $mail->hasTo($peopleInRoom[1]->email) && $mail->subject == 'Test subject' && $mail->message == 'Test message');
        Mail::assertQueued(FacilitiesEmail::class, fn ($mail) => $mail->hasTo($peopleInOtherRoom[0]->email) && $mail->subject == 'Test subject' && $mail->message == 'Test message');
        Mail::assertQueued(FacilitiesEmail::class, fn ($mail) => $mail->hasTo($peopleInOtherRoom[1]->email) && $mail->subject == 'Test subject' && $mail->message == 'Test message');
        // we also send a copy to the user who triggered the email
        Mail::assertQueued(FacilitiesEmail::class, fn ($mail) => $mail->hasTo($user->email) && $mail->subject == 'Test subject' && $mail->message == 'Test message');
    }

    /** @test */
    public function the_default_subject_on_the_email_form_is_appropriate()
    {
        Mail::fake();
        $user = User::factory()->create();
        $building1 = Building::factory()->create();
        $room1 = Room::factory()->create();

        $response = $this->actingAs($user)->get(route('email.building_form', $building1));

        $response->assertOk();
        $response->assertSeeInOrder(['Send email to people in', $building1->name]);

        $response = $this->actingAs($user)->get(route('email.room_form', $room1));

        $response->assertOk();
        $response->assertSeeInOrder(['Send email to people in', $room1->building->name, 'room', $room1->name]);
    }
}
