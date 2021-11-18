<?php

namespace App\Console\Commands;

use App\Mail\ArrivalsDeparturesMail;
use App\Models\People;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class NotifyAboutArrivalsAndDepartures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facilities:notify-about-arrivals-departures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email to facilites.admin_email about upcoming arrivals and departures';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $arrivals = People::where('start_at', '>', now())
            ->where('start_at', '<', now()->addDays(config('facilities.email_alert_days', 14)))
            ->orderBy('start_at')
            ->get();
        $departures = People::where('end_at', '>', now())
            ->where('end_at', '<', now()->addDays(config('facilities.email_alert_days', 14)))
            ->orderBy('end_at')
            ->get();

        Mail::to(config('facilities.admin_email'))->queue(new ArrivalsDeparturesMail($arrivals, $departures));

        return Command::SUCCESS;
    }
}
