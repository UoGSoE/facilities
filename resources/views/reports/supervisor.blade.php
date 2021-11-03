<x-layouts.app>
    <div class="d-flex justify-content-between">
        <h3>Supervisor {{ $supervisor->full_name }}</h3>
        <a class="btn btn-info text-white" href="mailto:{{ $supervisor->email }}"><i class="bi bi-envelope"></i></a>
    </div>
    <hr>
    <div class="d-flex justify-content-between">
        <div>
            <p>Supervisees</p>
            <h5>{{ $supervisor->supervisees->count() }}</h5>
        </div>
        <div>
            <p>Desks</p>
            <h5>{{ $supervisor->desk_count }}</h5>
        </div>
        <div>
            <p>Lockers</p>
            <h5>{{ $supervisor->locker_count }}</h5>
        </div>
        <div>
            <p>Buildings</p>
            <h5>{{ $supervisor->building_count }}</h5>
        </div>
    </div>
    <hr>
    <h4>Supervisees</h4>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Room(s)</th>
                <th>Locker(s)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($supervisor->supervisees as $supervisee)
                <tr
                    @if ($supervisee->isLeavingSoon()) class="table-warning" title="Leaving {{ $supervisee->end_at->format('d/m/Y') }}" @endif
                    @if ($supervisee->hasLeft()) class="table-danger" title="Left {{ $supervisee->end_at->format('d/m/Y') }}" @endif
                >
                    <td>{{ $supervisee->full_name }}</td>
                    <td>{{ $supervisee->email }}</td>
                    <td>
                        @foreach ($supervisee->desks as $desk)
                            <a href="{{ route('room.edit', $desk->room) }}">{{ $desk->room->building->name }} {{ $desk->room->name }}</a> desk {{ $desk->name }}
                        @endforeach
                    </td>
                    <td>
                        @foreach ($supervisee->lockers as $locker)
                            <a href="{{ route('room.edit', $locker->room) }}">{{ $locker->room->building->name }} {{ $locker->room->name }}</a> locker {{ $locker->name }}
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <h4>By Building</h4>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Building</th>
                <th>Room</th>
                <th>Desk(s)</th>
                <th>Locker(s)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($buildings as $building)
                @foreach ($building->rooms as $room)
                    <tr>
                        <td>{{ $building->name }}</td>
                        <td><a href="{{ route('room.edit', $room) }}">{{ $room->name }}</a></td>
                        <td>
                            @foreach ($room->desks as $desk)
                                @if (optional($desk->owner)->supervisor_id == $supervisor->id)
                                    <span
                                        @if ($desk->owner->isLeavingSoon()) class="bg-warning" title="{{ $desk->owner->full_name }} leaving {{ $desk->owner->end_at->format('d/m/Y') }}"
                                        @elseif ($desk->owner->hasLeft()) class="bg-danger text-white" title="{{ $desk->owner->full_name }} left {{ $desk->owner->end_at->format('d/m/Y') }}"
                                        @else title="{{ $desk->owner->full_name }}"
                                        @endif
                                    >
                                    {{ $desk->name }}
                                    </span>
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @foreach ($room->lockers as $locker)
                                @if (optional($locker->owner)->supervisor_id == $supervisor->id)
                                    <span
                                        @if ($locker->owner->isLeavingSoon()) class="bg-warning" title="{{ $locker->owner->full_name }} leaving {{ $locker->owner->end_at->format('d/m/Y') }}"
                                        @elseif ($locker->owner->hasLeft()) class="bg-danger text-white" title="{{ $locker->owner->full_name }} left {{ $locker->owner->end_at->format('d/m/Y') }}"
                                        @else title="{{ $locker->owner->full_name }}"
                                        @endif
                                    >
                                        {{ $locker->name }}
                                    </span>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
</x-layouts.app>
