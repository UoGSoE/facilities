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

    public function render()
    {
        return view('livewire.people-report', [
            'people' => $this->getPeople(),
        ]);
    }

    public function getPeople()
    {
        return People::withCount(['desks', 'lockers'])->with('supervisor')->orderByDesc('end_at')
            ->when(strlen($this->search) > 1, function ($query) {
                return $query->where('surname', 'like', "%{$this->search}%")->orWhere('forenames', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%");
            })
            ->when(intval($this->leavingWeeks) > 0, function ($query) {
                return $query->where('end_at', '<=', now()->addWeeks($this->leavingWeeks));
            })
            ->when($this->peopleType != 'any', function ($query) {
                return $query->where('type', '=', $this->peopleType);
            })
            ->paginate($this->perPage);
    }

    public function updating()
    {
        $this->resetPage();
    }
}
