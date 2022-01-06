<?php

namespace App\Mail;

use App\Models\People;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class YourRecentAllocation extends Mailable
{
    use Queueable, SerializesModels;

    public $personId;
    public $allocations = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(int $personId)
    {
        $this->personId = $personId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $person = People::find($this->personId);
        $desks = $person->desks()->recentlyAllocated()->with('room.building')->get();
        $lockers = $person->lockers()->recentlyAllocated()->with('room.building')->get();
        $this->allocations = $desks->merge($lockers);
        return $this->subject(config('facilites.email_prefix', '[Facilities]') . ' Your recent allocation')->markdown('emails.your_recent_allocation');
    }
}
