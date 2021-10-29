<x-layouts.app>
    <h3>Details for building {{ $building->name }}</h3>
    <div>
        <span class="bg-info text-white p-2">No Owner</span>
        <span class="bg-warning p-2">Leaving in < 28 days</span>
        <span class="bg-danger text-white p-2">Has Left</span>
    </div>
    @foreach ($building->rooms as $room)
        <hr>
        <div class="d-flex justify-content-between mb-4 bg-light p-4">
            <h5>Room {{ $room->name }}</h5>

            <a href="{{ route('room.edit', $room) }}" class="btn btn-secondary">Edit</a>
        </div>
        @foreach ($room->desks->chunk(4) as $someDesks)
            <div class="row">
                @foreach ($someDesks as $desk)
                    <div class="col p-2
                        @if (! $desk->owner) bg-info text-white @endif
                        @if ($desk->owner && $desk->owner->isLeavingSoon()) bg-warning @endif
                        @if ($desk->owner && $desk->owner->hasLeft()) bg-danger text-white @endif
                    ">
                        Desk {{ $desk->name }} - {{ $desk->owner ? $desk->owner->full_name : 'No owner' }}
                    </div>
                @endforeach
            </div>
        @endforeach
    @endforeach
</x-layouts.app>
