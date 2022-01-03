<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\People;
use Illuminate\Http\UploadedFile;
use Ohffs\SimpleSpout\ExcelSheet;
use App\Jobs\ProcessNewRequestRow;
use App\Mail\NewRequestsProcessed;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use App\Jobs\ProcessNewRequestsBatch;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;

class ImportNewRequestsTest extends TestCase
{
    use RefreshDatabase;

    public $singleRow = [
        132496,
        'Alistair McSmith',
        'a.mcsmith.1@research.gla.ac.uk',
        '01/10/2018',
        '',
        '01/10/2018',
        '... Technical Services',
        'Facilities',
        "Request for Facilities with a preferred completion date of:Request details:Hi - hope this finds you well,Previously my desk was located on level 7",
        '05/10/2021 10:46',
        10,
        '06/10/2021 10:46',
        '... Facilities',
        '',
        'Submitted',
        '(Other)',
    ];

    /** @test */
    public function we_can_process_a_single_row_from_the_spreadsheet()
    {
        ProcessNewRequestRow::dispatchSync($this->singleRow);

        tap(People::first(), function ($person) {
            $this->assertEquals('Alistair McSmith', $person->full_name);
            $this->assertEquals('a.mcsmith.1@research.gla.ac.uk', $person->email);
            $this->assertEquals('05/10/2021', $person->notes->first()->created_at->format('d/m/Y'));
            $this->assertEquals('06/10/2021', $person->notes->first()->updated_at->format('d/m/Y'));
            $this->assertEquals('IVANTI 132496 : Hi - hope this finds you well,Previously my desk was located on level 7', $person->notes->first()->body);
        });
    }

    /** @test */
    public function rows_which_contain_invalid_data_are_skipped_and_a_message_logged_using_redis()
    {
        Redis::shouldReceive('sadd')->once()->with('new_requests_import_errors_', 'The first column must be a number');

        ProcessNewRequestRow::dispatchSync(['wut', 'lol']);

        $this->assertEquals(0, People::count());
    }

    /** @test */
    public function importing_the_same_row_twice_doesnt_add_multiple_users_or_notes()
    {
        ProcessNewRequestRow::dispatchSync($this->singleRow);
        ProcessNewRequestRow::dispatchSync($this->singleRow);

        $this->assertEquals(1, People::count());
        tap(People::first(), function ($person) {
            $this->assertEquals('Alistair McSmith', $person->full_name);
            $this->assertEquals('a.mcsmith.1@research.gla.ac.uk', $person->email);
            $this->assertEquals('05/10/2021', $person->notes->first()->created_at->format('d/m/Y'));
            $this->assertEquals('06/10/2021', $person->notes->first()->updated_at->format('d/m/Y'));
            $this->assertEquals(1, $person->notes->count());
            $this->assertEquals('IVANTI 132496 : Hi - hope this finds you well,Previously my desk was located on level 7', $person->notes->first()->body);
        });
    }

    /** @test */
    public function we_can_get_just_the_ivanti_request_notes_for_a_person_after_the_import()
    {
        ProcessNewRequestRow::dispatchSync($this->singleRow);

        $this->assertEquals('IVANTI 132496 : Hi - hope this finds you well,Previously my desk was located on level 7', People::first()->ivantiNotes->first()->body);
    }

    /** @test */
    public function we_can_dispatch_a_job_with_multiple_rows_which_in_turns_dispatches_a_job_for_each_row()
    {
        Queue::fake();
        $user = User::factory()->create();

        $job = new ProcessNewRequestsBatch([
            $this->singleRow,
            $this->singleRow,
        ], $user->id);
        $job->dispatch([
            $this->singleRow,
            $this->singleRow,
        ], $user->id);
        $job->handle();

        Queue::assertPushed(ProcessNewRequestRow::class, 2);
    }

    /** @test */
    public function when_all_the_rows_have_been_processed_an_email_is_sent_to_the_person_who_kicked_off_the_import()
    {
        Mail::fake();
        $user = User::factory()->create();

        ProcessNewRequestsBatch::dispatchSync([
            $this->singleRow,
            $this->singleRow,
        ], $user->id);

        Mail::assertQueued(NewRequestsProcessed::class, 1);
        Mail::assertQueued(NewRequestsProcessed::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /** @test */
    public function users_can_upload_a_spreadsheet_via_the_web_which_kicks_off_the_queued_jobs()
    {
        $this->withoutExceptionHandling();
        Storage::fake();
        Queue::fake();
        $user = User::factory()->create();

        $fakeData = [
            $this->singleRow,
            $this->singleRow,
        ];
        $fakeSheetFilename = (new ExcelSheet)->generate($fakeData);

        $response = $this->actingAs($user)->get(route('import.new_requests_form'));

        $response->assertOk();
        $response->assertSee('Import new requests');

        $response = $this->actingAs($user)->post(route('import.new_requests'), [
            'sheet' => UploadedFile::fake()->createWithContent('test.xlsx', file_get_contents($fakeSheetFilename)),
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success', 'Import started - you will get an email once it is complete.');
        Queue::assertPushed(ProcessNewRequestsBatch::class, 1);
    }
}
