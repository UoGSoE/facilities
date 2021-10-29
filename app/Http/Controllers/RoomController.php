<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\People;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function edit(Room $room)
    {
        $room->load('desks.owner');
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

        $room->update(['name' => $request->name]);

        return redirect()->route('building.show', $room->building_id);
    }
}
