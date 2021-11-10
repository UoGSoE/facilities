<?php

namespace App\Http\Livewire;

use App\Models\Desk;
use App\Models\Locker;
use Livewire\Component;

class RecentReport extends Component
{
    public $filterWeeks = 4;
    public $peopleType = 'any';
    public $assetType = 'any';
    protected $recents;

    protected $queryString = [
        'filterWeeks',
        'peopleType',
        'assetType',
    ];

    public function mount()
    {
        $this->getRecentAllocations();
    }

    public function render()
    {
        return view('livewire.recent-report', [
            'assets' => $this->recents,
        ]);
    }

    public function updated()
    {
        $this->getRecentAllocations();
    }

    protected function getRecentAllocations()
    {
        $desks = collect([]);
        $lockers = collect([]);

        if ($this->assetType == 'any' || $this->assetType == 'desk') {
            $desks = Desk::recentlyAllocated($this->filterWeeks * 7)->with('owner', 'room.building')->get();
        }
        if ($this->assetType == 'any' || $this->assetType == 'locker') {
            $lockers = Locker::recentlyAllocated($this->filterWeeks * 7)->with('owner', 'room.building')->get();
        }

        $this->recents = $desks->merge($lockers)->sortByDesc('allocated_at')
            ->when(
                $this->peopleType != 'any',
                function ($recents) {
                    return $recents->filter(function ($recent) {
                        return $recent->owner->type == $this->peopleType;
                    });
                }
            );
    }
}
