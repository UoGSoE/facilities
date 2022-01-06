<?php

namespace App\Http\Controllers;

use App\Models\Desk;
use App\Models\Room;
use App\Models\Locker;
use App\Models\Building;
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
            'reallocate_to' => 'required|array',
            'reallocate_to.*' => 'required|integer',
        ]);

        $newBuildings = collect([]);
        foreach ($request->reallocate_to as $buildingId) {
            if ($buildingId == -1) {
                $buildingId = Building::inRandomOrder()->first()->id;
            }
            $newBuildings->push(Building::findOrFail($buildingId));
        }
        $unallocatedDesksInNewBuildings = $newBuildings->flatMap(fn ($building) => $building->getUnallocatedDesks())->shuffle();

        $desksToReallocate = $room->desks()->allocated()->with('owner')->get();
        foreach ($desksToReallocate as $desk) {
            $newDesk = $unallocatedDesksInNewBuildings->shift();
            if (! $newDesk) {
                $newDesk = Desk::unallocated()->where('room_id', '!=', $room->id)->inRandomOrder()->first();
                if (! $newDesk) {
                    return redirect()->route('room.edit', $room->id)->with('error', 'Ran out of spare desks while trying to re-allocate.');
                }
            }
            $newDesk->allocateTo($desk->owner);
            $desk->deallocate();
        }

        $unallocatedLockersInNewBuildings = $newBuildings->flatMap(fn ($building) => $building->getUnallocatedLockers())->shuffle();
        $lockersToReallocate = $room->lockers()->allocated()->with('owner')->get();
        foreach ($lockersToReallocate as $locker) {
            $newLocker = $unallocatedLockersInNewBuildings->shift();
            if (! $newLocker) {
                $newLocker = Locker::unallocated()->where('room_id', '!=', $room->id)->inRandomOrder()->first();
                if (! $newLocker) {
                    return redirect()->route('room.edit', $room->id)->with('error', 'Ran out of spare lockers while trying to re-allocate.');
                }
            }
            $newLocker->allocateTo($locker->owner);
            $locker->deallocate();
        }

        return redirect()->route('room.edit', $room->id)->with('success', 'Room re-allocated successfully.');
    }
}
