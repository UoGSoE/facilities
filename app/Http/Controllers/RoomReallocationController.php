<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomReallocationController extends Controller
{
    public function show(Room $room)
    {
        $room->load('building');
        return view('room.reallocation_form', [
            'room' => $room,
            'buildings' => Building::orderBy('name')->get(),
        ]);
    }

    public function update(Room $room, Request $request)
    {
        // @TODO this input is actually an array from a multi-select with `-1` as 'random'
        $request->validate([
            'reallocate_to' => 'required|integer',
        ]);

        $newBuilding = Building::findOrFail($request->reallocate_to);
        $oldBuilding = $room->building;

        $unallocatedDesksInNewBuilding = $newBuilding->getUnallocatedDesks()->shuffle();
        $desksToReallocate = $room->desks()->allocated()->with('owner')->get();
        foreach ($desksToReallocate as $desk) {
            $newDesk = $unallocatedDesksInNewBuilding->shift();
            // @TODO check if $newDesk is empty (ie, we are out of desks) and fetch other buildings as required
            $newDesk->update([
                'people_id' => $desk->owner->id,
            ]);
        }
        $room->desks()->allocated()->update(['people_id' => null]);

        $unallocatedLockersInNewBuilding = $newBuilding->getUnallocatedLockers()->shuffle();
        $lockersToReallocate = $room->lockers()->allocated()->with('owner')->get();
        foreach ($lockersToReallocate as $locker) {
            $newlocker = $unallocatedLockersInNewBuilding->shift();
            $newlocker->update([
                'people_id' => $locker->owner->id,
            ]);
        }
        $room->lockers()->allocated()->update(['people_id' => null]);
    }
}
