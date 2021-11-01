<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\Request;

class BuildingReportController extends Controller
{
    public function show()
    {
        $buildings = Building::with(['rooms.lockers.owner', 'rooms.desks.owner'])->orderBy('name')->get();
        foreach ($buildings as $building) {
            $building->desk_count = $building->rooms->reduce(fn ($carry, $room) => $carry + $room->desks->count(), 0);
            $building->desk_used_count = $building->rooms->reduce(fn ($carry, $room) => $carry + $room->desks->where('people_id', '!=', null)->count(), 0);
            $building->desk_soon_count = $building->rooms->reduce(fn ($carry, $room) => $carry + $room->desks->sum(fn ($desk) => optional($desk->owner)->isLeavingSoon()), 0);
            $building->locker_count = $building->rooms->reduce(fn ($carry, $room) => $carry + $room->lockers->count(), 0);
            $building->locker_used_count = $building->rooms->reduce(fn ($carry, $room) => $carry + $room->lockers->where('people_id', '!=', null)->count(), 0);
            $building->locker_soon_count = $building->rooms->reduce(fn ($carry, $room) => $carry + $room->lockers->sum(fn ($locker) => optional($locker->owner)->isLeavingSoon()), 0);
        }
        foreach ($buildings as $building) {
            $building->desk_used_percent = $building->desk_count ? round($building->desk_used_count / $building->desk_count * 100, 2) : 0;
            $building->locker_used_percent = $building->locker_count ? round($building->locker_used_count / $building->locker_count * 100, 2) : 0;
        }
        return view('reports.buildings', [
            'buildings' => $buildings,
        ]);
    }
}
