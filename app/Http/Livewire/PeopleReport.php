<?php

namespace App\Http\Livewire;

use App\Models\People;
use Livewire\Component;
use App\Models\Building;
use Livewire\WithPagination;

class PeopleReport extends Component
{
    use WithPagination;

    protected $queryString = ['search', 'leavingWeeks', 'peopleType', 'supervisor'];

    public $perPage = 100;
    public $search = '';
    public $leavingWeeks = '';
    public $peopleType = 'any';
    public $usergroup = '';
    public $supervisor = '';
    public $building = '';
    public $room = '';
    // this is purely here so we can do an assertion in PeopleReportTest::we_can_filter_the_report_in_various_ways
    public $peopleCount = 0;

    public function render()
    {
        $rooms = [];
        $buildings = Building::orderBy('name')->with('rooms')->get();
        foreach ($buildings as $building) {
            foreach ($building->rooms as $room) {
                $rooms[$building->id][] = (object) ['id' => $room->id, 'name' => $room->name];
            }
        }

        return view('livewire.people-report', [
            'people' => $this->getPeople(),
            'usergroups' => $this->getUsergroups(),
            'buildings' => $buildings,
            'rooms' => $rooms,
            'supervisors' => People::has('supervisees')->orderBy('surname')->get(),
        ]);
    }

    public function getPeople()
    {
        $people = $this->getPeopleQuery()->paginate($this->perPage);
        // this->peopleCount is purely here so we can do an assertion in PeopleReportTest::we_can_filter_the_report_in_various_ways
        $this->peopleCount = $people->total();

        return $people;
    }

    public function getUsergroups()
    {
        return People::select('usergroup')->distinct()->orderBy('usergroup')->get()->pluck('usergroup')->toArray();
    }

    public function exportCsv()
    {
        $people = $this->getPeopleQuery()->get();
        return response()->streamDownload(function () use ($people) {
            echo "Surname,Forenames,Email,Type,Supervisor,Started,Ends,Desks,Lockers,IT\n";
            foreach ($people as $person) {
                echo $person->surname . ',';
                echo $person->forenames . ',';
                echo $person->email . ',';
                echo $person->type . ',';
                echo optional($person->supervisor)->full_name . ',';
                echo $person->start_at->format('d/m/Y') . ',';
                echo $person->end_at->format('d/m/Y') . ',';
                echo $person->desks_count . ',';
                echo $person->lockers_count . ',';
                echo $person->it_assets_count . "\n";
            }
        }, 'facilities_all_people_' . now()->format('d-m-Y-H-i') . '.csv', ['Content-Type' => 'text/csv']);
    }

    public function updating()
    {
        $this->resetPage();
    }

    protected function getPeopleQuery()
    {
        return People::withCount(['desks', 'lockers', 'itAssets'])->with('supervisor')->orderByDesc('end_at')
            ->when(strlen($this->search) > 1, function ($query) {
                return $query->orWhere(function ($query) {
                    $query->where('surname', 'like', "%{$this->search}%")
                        ->orWhere('forenames', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when(intval($this->leavingWeeks) > 0, function ($query) {
                return $query->where('end_at', '<=', now()->addWeeks($this->leavingWeeks));
            })
            ->when($this->peopleType != 'any', function ($query) {
                return $query->where('type', '=', $this->peopleType);
            })
            ->when($this->supervisor, function ($query) {
                return $query->where('supervisor_id', '=', $this->supervisor);
            })
            ->when($this->usergroup != '', function ($query) {
                return $query->where('usergroup', '=', $this->usergroup);
            })
            ->when($this->room, function ($query) {
                return $query->where(function ($query) {
                    return $query->whereHas('desks', function ($query) {
                        return $query->where('room_id', '=', $this->room);
                    })->orWhereHas('lockers', function ($query) {
                        return $query->where('room_id', '=', $this->room);
                    });
                });
            })
            ->when($this->building && (! $this->room), function ($query) {
                return $query->where(function ($query) {
                    return $query->whereHas('desks', function ($query) {
                        return $query->whereHas('room', function ($query) {
                            return $query->where('building_id', '=', $this->building);
                        });
                    })->orWhereHas('lockers', function ($query) {
                        return $query->whereHas('room', function ($query) {
                            return $query->where('building_id', '=', $this->building);
                        });
                    });
                });
            })
            ;
    }
}
