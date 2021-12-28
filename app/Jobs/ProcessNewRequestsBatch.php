<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Jobs\ProcessNewRequestRow;
use App\Mail\NewRequestsProcessed;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ProcessNewRequestsBatch implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $rows;
    public $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $rows, int $userId)
    {
        $this->rows = $rows;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $userEmail = User::findOrFail($this->userId)->email;
        $batch = Bus::batch([]);
        foreach ($this->rows as $row) {
            $batch->add([new ProcessNewRequestRow($row)]);
        }
        $batch->allowFailures()
            ->finally(function (Batch $batch) use ($userEmail) {
                Mail::to($userEmail)->queue(new NewRequestsProcessed());
            })
            ->dispatch();
    }
}
