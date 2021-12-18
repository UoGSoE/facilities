<?php

namespace App\Http\Livewire;

use App\Models\People;
use Livewire\Component;
use Livewire\WithPagination;

class PeopleReport extends Component
{
    use WithPagination;

    protected $queryString = ['search', 'leavingWeeks', 'peopleType'];

    public $perPage = 100;
    public $search = '';
    public $leavingWeeks = '';
    public $peopleType = 'any';
    public $usergroup = '';

    public function render()
    {
        return view('livewire.people-report', [
            'people' => $this->getPeople(),
            'usergroups' => $this->getUsergroups(),
        ]);
    }

    public function getPeople()
    {
        return $this->getPeopleQuery()->paginate($this->perPage);
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
                return $query->where('surname', 'like', "%{$this->search}%")->orWhere('forenames', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%");
            })
            ->when(intval($this->leavingWeeks) > 0, function ($query) {
                return $query->where('end_at', '<=', now()->addWeeks($this->leavingWeeks));
            })
            ->when($this->peopleType != 'any', function ($query) {
                return $query->where('type', '=', $this->peopleType);
            })
            ->when($this->usergroup != '', function ($query) {
                return $query->where('usergroup', '=', $this->usergroup);
            });
    }
}
