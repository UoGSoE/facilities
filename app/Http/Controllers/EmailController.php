<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Mail\FacilitiesEmail;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function room(Room $room, Request $request)
    {
        $request->validate([
            'subject' => 'required|string|min:2',
            'message' => 'required|string|min:2',
        ]);

        $room->load('desks.owner', 'lockers.owner');
        $emails = $room->desks->map(fn ($desk) => $desk->owner->email);
        $emails = $emails->merge($room->lockers->map(fn ($locker) => $locker->owner->email))->unique();

        $emails->each(fn ($email) => Mail::to($email)->queue(new FacilitiesEmail($request->subject, $request->message)));

        return redirect()->back()->with('success', 'Email sent');
    }
}
