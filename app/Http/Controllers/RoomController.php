<?php

namespace App\Http\Controllers;

use App\Models\Desk;
use App\Models\Room;
use App\Models\Locker;
use App\Models\People;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RoomController extends Controller
{
    public function show(Room $room)
    {
        $room->load('desks.owner', 'building', 'lockers.owner');

        return view('room.show', [
            'room' => $room,
        ]);
    }

    public function create(Building $building)
    {
        return view('room.create', [
            'building' => $building,
            'room' => new Room([
                'building_id' => $building->id,
            ]),
        ]);
    }

    public function store(Building $building, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'desks' => 'required|integer|min:0',
            'lockers' => 'required|integer|min:0',
        ]);

        if ($building->rooms()->where('name', $request->name)->exists()) {
            $error = ValidationException::withMessages([
                'name' => ['The name has already been taken.'],
            ]);
            throw $error;
        }

        $room = new Room;
        $room->name = $request->name;
        $room->building_id = $building->id;
        $room->save();

        if ($request->desks > 0) {
            $newDesks = collect(range(1, $request->desks))->map(function ($number) use ($room) {
                return new Desk([
                    'name' => $number,
                ]);
            });
            $room->desks()->saveMany($newDesks);
        }

        if ($request->lockers > 0) {
            $newLockers = collect(range(1, $request->lockers))->map(function ($number) use ($room) {
                return new Locker([
                    'name' => $number,
                ]);
            });
            $room->lockers()->saveMany($newLockers);
        }

        return redirect()->route('building.show', [
            'building' => $building,
        ]);
    }

    public function edit(Room $room)
    {
        $room->load('desks.owner', 'lockers.owner');
        return view('room.edit', [
            'room' => $room,
            'people' => People::active()->orderBy('surname')->get(),
        ]);
    }

    public function update(Room $room, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        if ($room->building->rooms()->where('name', $request->name)->exists()) {
            $error = ValidationException::withMessages([
                'name' => ['The name has already been taken.'],
            ]);
            throw $error;
        }


        $room->update(['name' => $request->name]);

        return redirect()->route('building.show', $room->building_id);
    }

    public function delete(Room $room)
    {
        $room->load('building');
        return view('room.delete', [
            'room' => $room,
        ]);
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return redirect()->route('building.show', $room->building_id);
    }
}
