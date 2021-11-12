<div>
    <div class="row">
        <div class="col">
            <label for="" class="form-label">Arriving in the next</label>
            <div class="input-group">
                <input type="number" class="form-control" id="exampleFormControlInput1" placeholder="" wire:model="filterWeeks">
                <div class="input-group-text">weeks</div>
            </div>
        </div>
        <div class="col">
            <label for="" class="form-label">Type</label>
            <select class="form-select" aria-label="Type of Person" wire:model="filterType">
                <option value="any">Any</option>
                @foreach (app('people.types') as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <hr>

    <h4>Allocate To</h4>
    <div class="d-flex justify-content-left">
        <div class="input-group">
            <span class="input-group-text" id="basic-addon1">Building</span>
            <select wire:model="buildingId" class="form-select" aria-label="Default select example">
                <option value="-1">Anywhere</option>
                @foreach ($buildings as $building)
                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="ms-4 input-group">
            <span class="input-group-text" id="basic-addon1">Room</span>
            <select wire:model="roomId" class="form-select" aria-label="Default select example">
                <option value="-1">Any</option>
                @if ($selectedBuilding)
                    @foreach ($selectedBuilding->rooms->sortBy('name') as $room)
                        <option value="{{ $room->id }}">{{ $room->name }} (Free D: {{ $room->unallocated_desk_count }} L: {{ $room->unallocated_locker_count }})</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="ms-4">
            <button class="btn btn-primary" wire:click.prevent="allocate" @if ($warning) disabled @endif>Allocate</button>
        </div>
    </div>

    @if ($warning)
        <div class="alert alert-warning mt-2">{{ $warning }}</div>
    @endif

    <hr>


    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Supervisor</th>
                <th>Starts</th>
                <th>Type</th>
                <th width="10%">Desks Wanted</th>
                <th width="10%">Lockers Wanted</th>
                <th width="10%">Avanti No.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($people as $person)
                <tr>
                    <td><a href="{{ route('people.show', $person) }}">{{ $person->full_name }}</a></td>
                    <td><a href="mailto:{{ $person->email }}">{{ $person->email }}</a></td>
                    <td>
                        @if ($person->supervisor)
                            <a href="{{ route('reports.supervisor', $person->supervisor) }}">{{ $person->supervisor->full_name }}</a>
                        @endif
                    </td>
                    <td>{{ $person->start_at->format('d/m/Y') }}</td>
                    <td>{{ $person->type }}</td>
                    <td @error('deskAllocations.' . $person->id) class="bg-danger" @enderror>
                        <div>
                            <input type="number" min="0" max="5" wire:model.debounce.500ms="deskAllocations.{{ $person->id }}" class="form-control" id="exampleFormControlInput1">
                        </div>
                    </td>
                    <td @error('lockerAllocations.' . $person->id) class="bg-danger" @enderror>
                        <div>
                            <input type="number" min="0" max="5" wire:model.debounce.500ms="lockerAllocations.{{ $person->id }}" class="form-control" id="exampleFormControlInput1">
                        </div>
                    </td>
                    <td>
                        <div>
                            <input type="text" wire:model.debounce.500ms="avantiIds.{{ $person->id }}" class="form-control" id="exampleFormControlInput1">
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
