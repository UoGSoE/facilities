<x-layouts.app>
    <div class="d-flex justify-content-between">
        <div>
            <h3>Details for building {{ $building->name }}</h3>
        </div>
        <a href="" class="btn btn-light">Edit</a>
    </div>
    <hr>
    <div class="d-flex justify-content-between">
        <div>
            <span class="bg-info text-white p-2">No Owner</span>
            <span class="bg-warning p-2">Leaving in < 28 days</span>
            <span class="bg-danger text-white p-2">Has Left</span>
        </div>
        <a class="btn btn-light" href="{{ route('room.create', $building) }}">Add a new room</a>
    </div>
    @foreach ($building->rooms as $room)
        <hr>
        <div class="d-flex justify-content-between mb-4 bg-light p-4 shadow-sm">
            <h5>Room {{ $room->name }}</h5>

            <a href="{{ route('room.edit', $room) }}" class="btn btn-secondary">Edit</a>
        </div>
        @foreach ($room->desks->chunk(4) as $someDesks)
            <div class="row">
                @foreach ($someDesks as $desk)
                    <div
                        class="col p-2
                            @if (! $desk->owner) bg-info text-white @endif
                            @if ($desk->owner && $desk->owner->isLeavingSoon()) bg-warning @endif
                            @if ($desk->owner && $desk->owner->hasLeft()) bg-danger text-white @endif
                        "
                        title=""
                    >
                        Desk {{ $desk->name }} - {{ $desk->owner ? $desk->owner->full_name : 'No owner' }}
                        @if ($desk->owner && $desk->owner->isLeavingSoon()) ({{ $desk->owner->end_at->format('d/m/y') }}) @endif
                        @if ($desk->owner && $desk->owner->hasLeft()) ({{ $desk->owner->end_at->format('d/m/y') }}) @endif
                    </div>
                @endforeach
            </div>
        @endforeach

        <hr>
        @foreach ($room->lockers->chunk(4) as $someLockers)
        <div class="row">
            @foreach ($someLockers as $locker)
                <div class="col p-2
                    @if (! $locker->owner) bg-info text-white @endif
                    @if ($locker->owner && $locker->owner->isLeavingSoon()) bg-warning @endif
                    @if ($locker->owner && $locker->owner->hasLeft()) bg-danger text-white @endif
                ">
                    Locker {{ $locker->name }} - {{ $locker->owner ? $locker->owner->full_name : 'No owner' }}
                    @if ($locker->owner && $locker->owner->isLeavingSoon()) ({{ $locker->owner->end_at->format('d/m/y') }}) @endif
                    @if ($locker->owner && $locker->owner->hasLeft()) ({{ $locker->owner->end_at->format('d/m/y') }}) @endif
            </div>
            @endforeach
        </div>
        @endforeach

    @endforeach
</x-layouts.app>
