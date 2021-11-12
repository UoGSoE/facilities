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

    public function exportCsv()
    {
        $this->getRecentAllocations();
        return response()->streamDownload(function () {
            echo "Person,Type,Asset,Building,Room,Allocated,Avanti\n";
            foreach ($this->recents as $asset) {
                echo $asset->owner->full_name . ',';
                echo $asset->owner->type . ',';
                echo $asset->getPrettyName() . ',';
                echo $asset->room->building->name . ',';
                echo $asset->room->name . ',';
                echo $asset->allocated_at->format('d/m/Y') . ',';
                echo $asset->avanti_ticket_id . "\n";
            }
        }, 'recent_allocated_facilites_' . now()->format('d-m-Y-H-i') . '.csv', ['Content-Type' => 'text/csv']);
    }
}
