<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Desk;
use App\Models\Room;
use App\Models\User;
use App\Models\Locker;
use App\Models\People;
use App\Models\Building;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class NotesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function rooms_buildings_desks_lockers_and_people_can_have_notes_attached()
    {
        $room = Room::factory()->hasNotes(3)->create();
        $building = Building::factory()->hasNotes(2)->create();
        $desk = Desk::factory()->hasNotes(1)->create();
        $locker = Locker::factory()->hasNotes(4)->create();
        $person = People::factory()->hasNotes(5)->create();

        $this->assertCount(3, $room->notes);
        $this->assertCount(2, $building->notes);
        $this->assertCount(1, $desk->notes);
        $this->assertCount(4, $locker->notes);
        $this->assertCount(5, $person->notes);
    }

    /** @test */
    public function when_viewing_the_various_things_we_can_see_the_notes_editor()
    {
        $user = User::factory()->create();
        $room = Room::factory()->hasNotes(3)->create();
        $building = Building::factory()->hasNotes(2)->create();
        $person = People::factory()->hasNotes(5)->create();

        $this->actingAs($user)->get(route('room.show', $room))
            ->assertSeeLivewire('notes-editor');

        $this->actingAs($user)->get(route('building.show', $building))
            ->assertSeeLivewire('notes-editor');

        $this->actingAs($user)->get(route('people.show', $person))
            ->assertSeeLivewire('notes-editor');
    }

    /** @test */
    public function the_notes_editor_can_be_toggled_visible_or_not()
    {
        $user = User::factory()->create();
        $room = Room::factory()->hasNotes(3)->create();

        Livewire::actingAs($user)->test('notes-editor', ['model' => $room])
            ->assertDontSee($room->notes->first()->body)
            ->call('toggleVisible')
            ->assertSee($room->notes->first()->body)
            ->call('toggleVisible')
            ->assertDontSee($room->notes->first()->body);
        ;
    }

    /** @test */
    public function we_can_add_a_new_note()
    {
        $user = User::factory()->create();
        $room = Room::factory()->hasNotes(3)->create();

        Livewire::actingAs($user)->test('notes-editor', ['model' => $room])
            ->call('toggleVisible')
            ->assertSet('body', '')
            ->assertDontSee('This is a new note')
            ->set('body', 'This is a new note')
            ->call('save')
            ->assertSet('body', '')
            ->assertSee('This is a new note');
        ;
    }
}
