<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\People;
use Illuminate\Http\Request;

class SupervisorReportController extends Controller
{
    public function index()
    {
        $supervisors = People::has('supervisees')->with('supervisees.desks', 'supervisees.lockers', 'supervisees.itAssets')->orderBy('surname')->get();
        foreach ($supervisors as $supervisor) {
            $supervisor->desk_count = $supervisor->supervisees->reduce(fn ($carry, $person) => $carry + $person->desks->count(), 0);
            $supervisor->locker_count = $supervisor->supervisees->reduce(fn ($carry, $person) => $carry + $person->lockers->count(), 0);
            $supervisor->it_count = $supervisor->supervisees->reduce(fn ($carry, $person) => $carry + $person->itAssets->count(), 0);
        }
        return view('reports.supervisors', [
            'supervisors' => $supervisors,
        ]);
    }

    public function show(People $supervisor)
    {
        $buildings = Building::with('rooms.desks.owner', 'rooms.desks.room.building', 'rooms.lockers.owner', 'rooms.lockers.room.building')->orderBy('name')->get();
        $supervisor->load('supervisees.desks.room.building', 'supervisees.lockers.room.building');
        $supervisor->desk_count = $supervisor->supervisees->reduce(fn ($carry, $person) => $carry + $person->desks->count(), 0);
        $supervisor->locker_count = $supervisor->supervisees->reduce(fn ($carry, $person) => $carry + $person->lockers->count(), 0);
        $buildingIds = [];
        foreach ($supervisor->supervisees as $person) {
            foreach ($person->desks as $desk) {
                $buildingIds[] = $desk->room->building_id;
            }
            foreach ($person->lockers as $locker) {
                $buildingIds[] = $locker->room->building_id;
            }
        }
        $supervisor->building_count = count(array_unique($buildingIds));
        return view('reports.supervisor', [
            'supervisor' => $supervisor,
            'buildings' => $buildings,
        ]);
    }
}
