<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PendingAllocationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $assets;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($assets)
    {
        $this->assets = $assets;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(config('facilities.email_prefix') . ' New Allocations')->markdown('emails.pending_allocation');
    }
}
