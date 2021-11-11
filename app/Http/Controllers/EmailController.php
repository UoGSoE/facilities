<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Building;
use Illuminate\Http\Request;
use App\Mail\FacilitiesEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function showRoomForm(Room $room)
    {
        return view('email.room', [
            'room' => $room,
        ]);
    }

    public function room(Room $room, Request $request)
    {
        $request->validate([
            'subject' => 'required|string|min:2',
            'message' => 'required|string|min:2',
        ]);

        $room->load('desks.owner', 'lockers.owner');
        $emails = $room->desks->filter(fn ($desk) => $desk->isAllocated())->map(fn ($desk) => $desk->owner->email);
        $emails = $emails->merge($room->lockers->filter(fn ($locker) => $locker->isAllocated())->map(fn ($locker) => $locker->owner->email));
        $emails->push($request->user()->email); // so the person who triggered the email gets a copy for their records

        $emails
            ->unique()
            ->filter()
            ->each(
                fn ($email) => Mail::to($email)->later(
                    now()->addSeconds(rand(1, 300)),
                    new FacilitiesEmail($request->subject, $request->message)
                )
            );

        return redirect()->route('room.show', $room)->with('success', 'Email sent');
    }

    public function showBuildingForm(Building $building)
    {
        return view('email.building', [
            'building' => $building,
        ]);
    }

    public function building(Building $building, Request $request)
    {
        $request->validate([
            'subject' => 'required|string|min:2',
            'message' => 'required|string|min:2',
        ]);

        $building->load('rooms.desks.owner', 'rooms.lockers.owner');
        $emails = $building->rooms->flatMap(
            fn ($room) => $room->desks->filter(
                fn ($desk) => $desk->isAllocated()
            )->map(fn ($desk) => $desk->owner->email)
        );
        $emails = $emails->merge(
            $building->rooms->flatMap(
                fn ($room) => $room->lockers->filter(
                    fn ($locker) => $locker->isAllocated()
                )->map(fn ($locker) => $locker->owner->email)
            )
        );
        $emails->push($request->user()->email); // so the person who triggered the email gets a copy for their records

        $emails
            ->unique()
            ->filter()
            ->each(
                fn ($email) => Mail::to($email)->later(
                    now()->addSeconds(rand(1, 300)),
                    new FacilitiesEmail($request->subject, $request->message)
                )
            );

        return redirect()->route('building.show', $building)->with('success', 'Email sent');
    }
}
