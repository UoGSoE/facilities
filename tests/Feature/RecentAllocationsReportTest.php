<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Desk;
use App\Models\User;
use App\Models\Locker;
use App\Models\People;
use Livewire\Livewire;
use App\Mail\YourRecentAllocation;
use App\Http\Livewire\RecentReport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecentAllocationsReportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function we_can_see_the_recent_allocations_report_page()
    {
        $user = User::factory()->create();
        $person = People::factory()->create();
        $desk = Desk::factory()->create();
        $desk->allocateTo($person);

        $response = $this->actingAs($user)->get(route('reports.recent'));

        $response->assertOk();
        $response->assertSee('Recently allocated assets');
        $response->assertSeeLivewire('recent-report');
        $response->assertSee($desk->name);
        $response->assertSee($desk->room->name);
        $response->assertSee($desk->room->building->name);
    }

    /** @test */
    public function we_can_export_the_recent_allocations_as_a_csv_file()
    {
        $this->markTestSkipped('TODO - waiting to see how to test a file download from livewire - https://github.com/livewire/livewire/discussions/4205');
        $user = User::factory()->create();
        $person = People::factory()->create();
        $desk = Desk::factory()->create();
        $desk->allocateTo($person);

        Livewire::actingAs($user)
            ->test(RecentReport::class)
            ->call('exportCsv')
            ->assertPayloadSet('effects.download', 'foo.csv');
    }

    /** @test */
    public function we_can_email_a_selected_group_of_people_with_their_allocations()
    {
        Mail::fake();
        $user = User::factory()->create();
        $person1 = People::factory()->create();
        $person2 = People::factory()->create();
        $person3 = People::factory()->create();
        $person4 = People::factory()->create();
        $desk1 = Desk::factory()->create();
        $desk2 = Desk::factory()->create();
        $locker1 = Locker::factory()->create();
        $locker2 = Locker::factory()->create();
        $desk1->allocateTo($person1);
        $desk2->allocateTo($person2);
        $locker1->allocateTo($person3);
        $locker2->allocateTo($person4);

        Livewire::actingAs($user)->test(RecentReport::class)
            ->set('mailToIds', [$person1->id, $person3->id])
            ->call('sendEmail')
            ->assertSet('mailToIds', [$person1->id, $person3->id]);

        Mail::assertQueued(YourRecentAllocation::class, 2);
        Mail::assertQueued(YourRecentAllocation::class, fn ($mail) => $mail->hasTo($person1->email));
        Mail::assertQueued(YourRecentAllocation::class, fn ($mail) => $mail->hasTo($person3->email));
    }

    /** @test */
    public function we_toggle_all_the_users_as_being_sent_a_mail()
    {
        $user = User::factory()->create();
        $person1 = People::factory()->create();
        $person2 = People::factory()->create();
        $person3 = People::factory()->create();
        $person4 = People::factory()->create();
        $desk1 = Desk::factory()->create();
        $desk2 = Desk::factory()->create();
        $locker1 = Locker::factory()->create();
        $locker2 = Locker::factory()->create();
        $desk1->allocateTo($person1);
        $desk2->allocateTo($person2);
        $locker1->allocateTo($person3);
        $locker2->allocateTo($person4);

        Livewire::actingAs($user)->test(RecentReport::class)
            ->set('mailToIds', [$person1->id, $person3->id])
            ->call('toggleAllEmails')
            ->assertSet('mailToIds', [])
            ->call('toggleAllEmails')
            ->assertSet('mailToIds', [$person1->id, $person2->id, $person3->id, $person4->id])
            ->call('toggleAllEmails')
            ->assertSet('mailToIds', []);
    }
}
