<?php

namespace App\Http\Livewire;

use App\Models\Desk;
use App\Models\Locker;
use App\Models\People;
use Livewire\Component;
use App\Mail\YourRecentAllocation;
use Illuminate\Support\Facades\Mail;

class RecentReport extends Component
{
    public $filterWeeks = 4;
    public $peopleType = 'any';
    public $assetType = 'any';
    public $mailToIds = [];
    protected $recents = [];

    protected $queryString = [
        'filterWeeks',
        'peopleType',
        'assetType',
    ];

    public function render()
    {
        return view('livewire.recent-report', [
            'assets' => $this->getRecentAllocations(),
        ]);
    }

    public function updated($name, $value)
    {
        if ($name != 'mailToIds') {
            $this->mailToIds = [];
        }
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
        return $this->recents;
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

    public function sendEmail()
    {
        if (count($this->mailToIds) == 0) {
            return;
        }

        foreach ($this->mailToIds as $peopleId) {
            $person = People::findOrFail($peopleId);
            Mail::to($person)->later(now()->addSeconds(rand(10, 300)), new YourRecentAllocation($peopleId));
        }

        session()->flash('emailMessage', 'Emails sent!');
    }
}
