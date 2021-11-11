        <hr>
        <div class="d-flex justify-content-between mb-4 bg-light p-4 shadow-sm">
            <h5>
                Room {{ $room->name }}
                <a href="{{ route('email.room_form', $room) }}" class="btn btn-light btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                    </svg>
                </a>
            </h5>

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
