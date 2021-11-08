<div>
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
            <button class="btn btn-primary" wire:click.prevent="allocate">Allocate</button>
        </div>
    </div>

    @if ($warning)
        <div class="alert alert-warning mt-2">{{ $warning }}</div>
    @endif

    <hr>

    <table class="table">
        <thead>
            <tr>
                <th>
                    <button class="btn btn-sm text-danger text-start" title="Remove from pending">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                          </svg>
                    </button>
                </th>
                <th>Name</th>
                <th>Email</th>
                <th>Supervisor</th>
                <th>Starts</th>
                <th>Type</th>
                <th width="10%">Desks Wanted</th>
                <th width="10%">Lockers Wanted</th>
            </tr>
        </thead>
        <tbody>
            @foreach($people as $person)
                <tr>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="allocate.{{ $person->id }}" value="{{ $person->id }}" id="flexCheckDefault">
                          </div>
                    </td>
                    <td><a href="">{{ $person->full_name }}</a></td>
                    <td><a href="mailto:{{ $person->email }}">{{ $person->email }}</a></td>
                    <td>
                        {{ optional($person->supervisor)->full_name }}
                    </td>
                    <td>{{ $person->start_at->format('d/m/Y') }}</td>
                    <td>{{ $person->type }}</td>
                    <td>
                        <div>
                            <input type="number" min="0" max="5" wire:model.debounce.500ms="deskAllocations.{{ $person->id }}" class="form-control" id="exampleFormControlInput1">
                        </div>
                    </td>
                    <td>
                        <div>
                            <input type="number" min="0" max="5" wire:model.debounce.500ms="lockerAllocations.{{ $person->id }}" class="form-control" id="exampleFormControlInput1">
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
