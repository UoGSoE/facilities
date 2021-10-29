<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\People;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function show()
    {
        $activePeopleCount = People::active()->count();
        $buildings = Building::with(['rooms.lockers', 'rooms.desks'])->orderBy('name')->get();
        foreach ($buildings as $building) {
            $building->desk_count = $building->rooms->reduce(fn ($carry, $room) => $carry + $room->desks->count());
            $building->desk_used_count = $building->rooms->reduce(fn ($carry, $room) => $carry + $room->desks->where('people_id', '!=', null)->count());
            $building->locker_count = $building->rooms->reduce(fn ($carry, $room) => $carry + $room->lockers->count());
            $building->locker_used_count = $building->rooms->reduce(fn ($carry, $room) => $carry + $room->lockers->where('people_id', '!=', null)->count());
        }
        $totals = [
            'room_counts' => $buildings->reduce(fn ($carry, $building) => $carry + $building->rooms->count()),
            'desk_count' => $buildings->reduce(fn ($carry, $building) => $carry + $building->desk_count),
            'desk_used_count' => $buildings->reduce(fn ($carry, $building) => $carry + $building->desk_used_count),
            'locker_count' => $buildings->reduce(fn ($carry, $building) => $carry + $building->locker_count),
            'locker_used_count' => $buildings->reduce(fn ($carry, $building) => $carry + $building->locker_used_count),
        ];

        return view('home', [
            'buildings' => $buildings,
            'totals' => $totals,
            'activePeopleCount' => $activePeopleCount,
        ]);
    }
}
