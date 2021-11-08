<?php

namespace App\Http\Livewire;

use App\Models\Desk;
use App\Models\Room;
use App\Models\Locker;
use Livewire\Component;
use App\Models\Building;
use Illuminate\Support\Collection;

class PendingPeopleReport extends Component
{
    public $people;
    public $buildings;
    public $selectedBuilding;
    public $buildingId = -1;
    public $roomId = -1;
    public $allocate = [];
    public $deskAllocations = [];
    public $lockerAllocations = [];
    public $warning = '';

    public function mount(Collection $people)
    {
        $this->people = $people;
        $this->people->each(function ($person) {
            $this->allocate[$person->id] = false;
            $this->deskAllocations[$person->id] = 0;
            $this->lockerAllocations[$person->id] = 0;
        });
        $this->buildings = Building::with('rooms')->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.pending-people-report');
    }

    public function updatedBuildingId()
    {
        $this->roomId = -1;
        $this->warning = '';
        if ($this->buildingId != -1) {
            $this->selectedBuilding = Building::with('rooms.desks', 'rooms.lockers')->find($this->buildingId);
        } else {
            $this->selectedBuilding = null;
        }
        $this->checkCapacity();
    }

    public function updatedRoomId()
    {
        $this->checkCapacity();
    }

    public function updatedDeskAllocations()
    {
        $this->checkCapacity();
    }

    public function updatedLockerAllocations()
    {
        $this->checkCapacity();
    }

    protected function checkCapacity()
    {
        $this->warning = '';
        $this->checkTotalCapacity();
        $this->checkBuildingCapacity();
        $this->checkRoomCapacity();
    }

    protected function checkTotalCapacity()
    {
        if ($this->buildingId != -1) {
            return;
        }
        $totalDesks = Building::with('rooms.desks', 'rooms.lockers')->get()->map(fn ($building) => $building->unallocated_desk_count)->sum();
        $totalLockers = Building::with('rooms.desks', 'rooms.lockers')->get()->map(fn ($building) => $building->unallocated_locker_count)->sum();

        if (collect($this->deskAllocations)->sum() > $totalDesks) {
            $this->warning .= 'Not enough desks. ';
        }
        if (collect($this->lockerAllocations)->sum() > $totalLockers) {
            $this->warning .= 'Not enough lockers. ';
        }
    }

    protected function checkBuildingCapacity()
    {
        if ($this->buildingId == -1 || $this->roomId != -1) {
            return;
        }

        if (collect($this->deskAllocations)->sum() > $this->selectedBuilding->unallocated_desk_count) {
            $this->warning .= 'Not enough desks in that building. ';
        }
        if (collect($this->lockerAllocations)->sum() > $this->selectedBuilding->unallocated_locker_count) {
            $this->warning .= 'Not enough lockers in that building. ';
        }
    }

    protected function checkRoomCapacity()
    {
        if ($this->roomId == -1) {
            return;
        }
        $room = Room::with('desks', 'lockers')->find($this->roomId);
        if (collect($this->deskAllocations)->sum() > $room->desks()->unallocated()->count()) {
            $this->warning .= 'Not enough desks in that room. ';
        }
        if (collect($this->lockerAllocations)->sum() > $room->lockers()->unallocated()->count()) {
            $this->warning .= 'Not enough lockers in that room. ';
        }
    }

    public function allocate()
    {
        if (collect($this->deskAllocations)->sum() == 0 && collect($this->lockerAllocations)->sum() == 0) {
            $this->warning = 'No allocations requested.';
            return;
        }

        collect($this->deskAllocations)->filter(fn ($required) => $required > 0)->each(function ($desksRequired, $personId) {
            $desks = $this->findUnallocatedDesks($desksRequired);
            $desks->each->update(['people_id' => $personId]);
        });
        collect($this->lockerAllocations)->filter(fn ($required) => $required > 0)->each(function ($lockersRequired, $personId) {
            $lockers = $this->findUnallocatedlockers($lockersRequired);
            $lockers->each->update(['people_id' => $personId]);
        });
    }

    protected function findUnallocatedDesks($numberRequired = 1)
    {
        if ($this->roomId > -1) {
            return Room::find($this->roomId)->desks()->unallocated()->inRandomOrder()->take($numberRequired)->get();
        }

        if ($this->buildingId > -1) {
            return Building::with('rooms.desks', 'rooms.lockers')->find($this->buildingId)->getUnallocatedDesks()->shuffle()->take($numberRequired);
        }

        return Desk::unallocated()->inRandomOrder()->take($numberRequired)->get();
    }

    protected function findUnallocatedLockers($numberRequired = 1)
    {
        if ($this->roomId > -1) {
            return Room::find($this->roomId)->lockers()->unallocated()->inRandomOrder()->take($numberRequired)->get();
        }

        if ($this->buildingId > -1) {
            return Building::with('rooms.desks', 'rooms.lockers')->find($this->buildingId)->getUnallocatedLockers()->shuffle()->take($numberRequired);
        }

        return Locker::unallocated()->inRandomOrder()->take($numberRequired)->get();
    }
}
