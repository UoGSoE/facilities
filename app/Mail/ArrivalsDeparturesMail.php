<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ArrivalsDeparturesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $arrivals;

    public $departures;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Collection $arrivals, Collection $departures)
    {
        $this->arrivals = $arrivals;
        $this->departures = $departures;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(config('facilities.email_prefix') . ' Arrivals and Departures')->markdown('emails.arrivals_departures');
    }
}
