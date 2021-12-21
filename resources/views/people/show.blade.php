<x-layouts.app>
    @section('title') {{ $person->full_name }} @endsection
    <h3>
        Details for {{ $person->full_name }}
        @livewire('notes-editor', ['model' => $person])
    </h3>
    <hr>
    <div class="d-flex justify-content-between">
        <div>
            <span>Email</span>
            <h4><a href="mailto:{{ $person->email }}">{{ $person->email }}</a></h4>
        </div>
        <div>
            <span>Supervisor</span>
            <h4>
                @if ($person->supervisor)
                    <a href="{{ route('reports.supervisor', $person->supervisor) }}">
                        {{ $person->supervisor->full_name }}
                    </a>
                @endif
            </h4>
        </div>
        <div>
            <span>Start</span>
            <h4>{{ $person->start_at->format('d/m/Y') }}</h4>
        </div>
        <div>
            <span>End</span>
            <h4
                class="
                @if ($person->isLeavingSoon()) text-warning @endif
                @if ($person->hasLeft()) text-danger @endif
                "
            >
                {{ $person->end_at->format('d/m/Y') }}
            </h4>
        </div>
    </div>
    <hr>
    <table class="table">
        <thead>
            <tr>
                <th>Allocated</th>
                <th>Building</th>
                <th>Room</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($person->desks as $desk)
                <tr>
                    <td>
                        <a href="">
                            Desk {{ $desk->name }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('building.show', $desk->room->building) }}">{{ $desk->room->building->name }}</a>
                    </td>
                    <td>
                        <a href="{{ route('room.show', $desk->room) }}">{{ $desk->room->name }}</a>
                    </td>
                </tr>
            @endforeach
            @foreach ($person->lockers as $locker)
                <tr>
                    <td>
                        <a href="">
                            Locker {{ $locker->name }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('building.show', $locker->room->building) }}">{{ $locker->room->building->name }}</a>
                    </td>
                    <td>
                        <a href="{{ route('room.show', $locker->room) }}">{{ $locker->room->name }}</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.app>
