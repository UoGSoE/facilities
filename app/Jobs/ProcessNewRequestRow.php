<?php

namespace App\Jobs;

use App\Models\People;
use Carbon\Carbon;
use DateTime;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class ProcessNewRequestRow implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $row;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $row)
    {
        $this->row = $row;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $validator = Validator::make($this->row, [
            '0' => 'required|numeric',
            '1' => 'required|string',
            '2' => 'required|email',
        ], [
            '0.numeric' => 'The first column must be a number',
            '1.string' => 'The second column should be persons name',
            '2.email' => 'The third column should be a valid email address',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            Redis::sadd('new_requests_import_errors_' . $this->batchId, implode(', ', $validator->errors()->all()));
            return;
        }

        $nameParts = explode(' ', $this->row[1]);

        $person = People::firstOrCreate(['email' => strtolower(trim($this->row[2]))], [
            'username' => Str::random(10),
            'surname' => $nameParts[array_key_last($nameParts)],
            'forenames' => implode(' ', array_slice($nameParts, 0, -1)),
            'email' => strtolower(trim($this->row[2])),
            'start_at' => $this->figureOutStartDate(),
        ]);

        $notePrefix = 'IVANTI ' . $this->row[0] . ' : ';

        if ($person->notes()->where('body', 'like', $notePrefix . '%')->count() === 0) {
            $person->notes()->create([
                'body' => $this->replaceIvantiGubbins($this->row[8], $notePrefix),
                'created_at' => $this->cellAsDateObject($this->row[9]),
                'updated_at' => $this->cellAsDateObject($this->row[11]),
            ]);
            $person->flagNewRequest();
        }
    }

    /**
     * The cell with the ivanti request details comes prefixed with "Request for Facilities with a preferred completion date of:Request details:".
     * So we strip that out and replace it with a helpful prefix of our own
     */
    protected function replaceIvantiGubbins(string $text, string $notePrefix)
    {
        return preg_replace('/.+Request details:/', $notePrefix, $text);
    }

    protected function figureOutStartDate(): \DateTime
    {
        if ($this->row[5]) {
            return $this->cellAsDateObject($this->row[5]);
        }
        if ($this->row[3]) {
            return $this->cellAsDateObject($this->row[3]);
        }
        if ($this->row[4]) {
            return $this->cellAsDateObject($this->row[4]);
        }

        // default to today if there's no start date found - not sure this is right, but meh.
        return now();
    }

    /**
     * Depending on the context - sometimes date cells are automatically converted to datetime objects
     * already, sometimes they are just plain strings.
     */
    protected function cellAsDateObject($cellValue): \DateTime
    {
        if (is_object($cellValue)) {
            return $cellValue;
        }

        if (str_contains($cellValue, ':')) {
            return Carbon::createFromFormat('d/m/Y H:i', $cellValue);
        }

        return Carbon::createFromFormat('d/m/Y', $cellValue);
    }
}
