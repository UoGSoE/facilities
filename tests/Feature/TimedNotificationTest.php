<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\People;
use App\Mail\ArrivalsDeparturesMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TimedNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_email_with_upcoming_arrivals_and_departures_is_sent_to_the_configured_email_address()
    {
        config(['facilities.admin_email' => 'admin@example.com']);
        config(['facilities.email_alert_days' => 7]);
        Mail::fake();
        $leavingUsers = People::factory()->count(2)->create(['end_at' => now()->addDays(3)]);
        $arrivingUsers = People::factory()->count(2)->create(['start_at' => now()->addDays(3)]);
        $leavingFarAwayUsers = People::factory()->count(2)->create(['end_at' => now()->addDays(15)]);
        $arrivingFarAwayUsers = People::factory()->count(2)->create(['start_at' => now()->addDays(15)]);

        $this->artisan('facilities:notify-about-arrivals-departures');

        Mail::assertQueued(ArrivalsDeparturesMail::class, 1);
        Mail::assertQueued(ArrivalsDeparturesMail::class, function ($mail) use ($leavingUsers, $arrivingUsers) {
            return $mail->hasTo('admin@example.com') &&
                $mail->arrivals->count() === 2 &&
                $mail->departures->count() === 2 &&
                $mail->arrivals->contains($arrivingUsers[0]) &&
                $mail->arrivals->contains($arrivingUsers[1]) &&
                $mail->departures->contains($leavingUsers[0]) &&
                $mail->departures->contains($leavingUsers[1]);
        });
    }

    /** @test */
    public function the_email_displays_the_correct_information()
    {
        config(['facilities.admin_email' => 'admin@example.com']);
        config(['facilities.email_alert_days' => 7]);
        $leavingUsers = People::factory()->count(2)->create(['end_at' => now()->addDays(3)]);
        $arrivingUsers = People::factory()->count(2)->create(['start_at' => now()->addDays(3)]);
        $leavingFarAwayUsers = People::factory()->count(2)->create(['end_at' => now()->addDays(15)]);
        $arrivingFarAwayUsers = People::factory()->count(2)->create(['start_at' => now()->addDays(15)]);

        $mailable = new ArrivalsDeparturesMail($arrivingUsers, $leavingUsers);

        $mailable->assertSeeInHtml($leavingUsers[0]->full_name);
        $mailable->assertSeeInHtml($leavingUsers[1]->full_name);
        $mailable->assertSeeInHtml($arrivingUsers[0]->full_name);
        $mailable->assertSeeInHtml($arrivingUsers[1]->full_name);
        $mailable->assertDontSeeInHtml($leavingFarAwayUsers[0]->full_name);
        $mailable->assertDontSeeInHtml($leavingFarAwayUsers[1]->full_name);
        $mailable->assertDontSeeInHtml($arrivingFarAwayUsers[0]->full_name);
        $mailable->assertDontSeeInHtml($arrivingFarAwayUsers[1]->full_name);
    }

    /** @test */
    public function the_command_to_run_the_notification_is_registered_with_laravel()
    {
        $this->assertCommandIsScheduled('facilities:notify-about-arrivals-departures');
    }
}
