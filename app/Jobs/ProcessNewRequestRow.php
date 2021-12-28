<?php

namespace App\Jobs;

use App\Models\People;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

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
        $nameParts = explode(' ', $this->row[1]);

        $person = People::firstOrCreate(['email' => strtolower(trim($this->row[2]))], [
            'username' => Str::random(10),
            'surname' => $nameParts[array_key_last($nameParts)],
            'forenames' => implode(' ', array_slice($nameParts, 0, -1)),
            'email' => strtolower(trim($this->row[2])),
            'start_at' => Carbon::createFromFormat('d/m/Y', $this->row[5]),
        ]);

        $notePrefix = 'IVANTI ' . $this->row[0] . ' : ';

        if ($person->notes()->where('body', 'like', $notePrefix . '%')->count() === 0) {
            $person->notes()->create([
                'body' => $this->replaceIvantiGubbins($this->row[8], $notePrefix),
                'created_at' => Carbon::createFromFormat('d/m/Y H:i', $this->row[9]),
                'updated_at' => Carbon::createFromFormat('d/m/Y H:i', $this->row[11]),
            ]);
        }
    }

    protected function replaceIvantiGubbins(string $text, string $notePrefix)
    {
        return preg_replace('/.+Request details:/', $notePrefix, $text);
    }
}
