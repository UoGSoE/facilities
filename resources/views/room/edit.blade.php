<x-layouts.app>
    <h3>Edit room {{ $room->name }} in building <a href="{{ route('building.show', $room->building) }}">{{ $room->building->name }}</a></h3>
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
        @foreach ($room->desks->chunk(4) as $someDesks)
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
                                    >
                                        Desk {{ $desk->name }}
                                    </div>
                                    <select class="form-select form-select-sm" aria-label="Select person using Desk {{ $desk->name }}">
                                        <option value="">No-one</option>
                                        @foreach ($people as $person)
                                            <option value="{{ $person->id }}" @if ($desk->people_id == $person->id) selected @endif>{{ $person->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                    </div>
                @endforeach
            </div>
        @endforeach
</x-layouts.app>
