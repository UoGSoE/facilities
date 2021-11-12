<x-layouts.app>
    @section('title') Edit room {{ $room->name }} @endsection
    <div class="d-flex justify-content-between">
        <h3>Edit room {{ $room->name }} in building <a href="{{ route('building.show', $room->building) }}">{{ $room->building->name }}</a></h3>
        <span>
            <a href="{{ route('room.reallocate', $room->id) }}" class="btn btn-secondary">Reallocate Everyone</a>
            <a href="{{ route('room.delete', $room->id) }}" class="btn btn-warning">Delete Room</a>
        </span>
    </div>
    <div class="mt-4">
        <span class="bg-info text-white p-2">No Owner</span>
        <span class="bg-warning p-2">Leaving in < 28 days</span>
        <span class="bg-danger text-white p-2">Owner Has Left</span>
    </div>
    <hr>
        <form action="" method="post" class="row row-cols-lg-auto g-3 align-items-center d-flex justify-content-between">
            @csrf
                <div class="col-12">
                    <div class="input-group">
                        <div class="input-group-text">Name</div>
                        <input type="text" class="form-control" id="room_name" name="room_name" value="{{ $room->name }}" required>
                    </div>
                </div>

                    <div class="col-12">
                        <div class="input-group">
                            <label for="desks" class="visually-hidden">No. Desks</label>
                            <div class="input-group-text">Desks</div>
                            <input type="number" class="form-control" id="desks" name="desks" value="{{ $room->desks->count() }}" min="0">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <label for="lockers" class="visually-hidden">No. Lockers</label>
                            <div class="input-group-text">Lockers</div>
                            <input type="number" class="form-control" id="lockers" name="lockers" value="{{ $room->lockers->count() }}" min="0">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <button class="btn btn-secondary">Update</button>
                        </div>
                    </div>
        </form>
        <hr>
        @foreach ($room->desks->chunk(3) as $someDesks)
            <div class="row">
                @foreach ($someDesks as $desk)
                    <div class="col p-2">
                            <div class="col-12">
                                <div class="input-group">
                                    <div class="input-group-text
                                        @if (! $desk->owner) bg-info text-white @endif
                                        @if ($desk->owner && $desk->owner->isLeavingSoon()) bg-warning @endif
                                        @if ($desk->owner && $desk->owner->hasLeft()) bg-danger text-white @endif
                                    "
                                        @if ($desk->owner && $desk->owner->hasLeft()) title="Owner was {{ $desk->owner->full_name }}" @endif
                                        @if ($desk->owner && $desk->owner->isLeavingSoon()) title="Leaving {{ $desk->owner->end_at->format('d/m/Y') }}" @endif
                                    >
                                        Desk {{ $desk->name }}
                                    </div>
                                    <select class="form-select form-select-sm" aria-label="Select person using Desk {{ $desk->name }}">
                                        <option value="">No-one</option>
                                        @foreach ($people as $person)
                                            <option value="{{ $person->id }}" @if ($desk->people_id == $person->id) selected @endif>{{ $person->full_name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control" placeholder="Avanti No." value="{{ $desk->avanti_ticket_id }}" aria-label="Avanti Ticket Number" aria-describedby="basic-addon2">
                                </div>
                            </div>
                    </div>
                @endforeach
            </div>
        @endforeach

        <hr>
        @foreach ($room->lockers->chunk(3) as $someLockers)
            <div class="row">
                @foreach ($someLockers as $locker)
                    <div class="col p-2">
                            <div class="col-12">
                                <div class="input-group">
                                    <div class="input-group-text
                                        @if (! $locker->owner) bg-info text-white @endif
                                        @if ($locker->owner && $locker->owner->isLeavingSoon()) bg-warning @endif
                                        @if ($locker->owner && $locker->owner->hasLeft()) bg-danger text-white @endif
                                    "
                                        @if ($locker->owner && $locker->owner->hasLeft()) title="Owner was {{ $locker->owner->full_name }}" @endif
                                        @if ($locker->owner && $locker->owner->isLeavingSoon()) title="Leaving {{ $locker->owner->end_at->format('d/m/Y') }}" @endif
                                    >
                                        Locker {{ $locker->name }}
                                    </div>
                                    <select class="form-select form-select-sm" aria-label="Select person using locker {{ $locker->name }}">
                                        <option value="">No-one</option>
                                        @foreach ($people as $person)
                                            <option value="{{ $person->id }}" @if ($locker->people_id == $person->id) selected @endif>{{ $person->full_name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control" placeholder="Avanti No." value="{{ $locker->avanti_ticket_id }}" aria-label="Avanti Ticket Number" aria-describedby="basic-addon2">
                                </div>
                            </div>
                    </div>
                @endforeach
            </div>
        @endforeach

</x-layouts.app>
