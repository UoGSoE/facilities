<div>
    <div class="row mb-3">
        <div class="col">
            <label for="" class="form-label">Leaving by</label>
            <div class="input-group">
                <input type="number" class="form-control" id="exampleFormControlInput1" placeholder="" wire:model="leavingWeeks">
                <div class="input-group-text">weeks</div>
            </div>
        </div>
        <div class="col">
            <label for="" class="form-label">Type</label>
            <select class="form-select" aria-label="Type of Person" wire:model="peopleType">
                <option value="any">Any</option>
                <option value="{{ \App\Models\People::TYPE_PGR }}">PGR</option>
                <option value="pdra">PDRA</option>
                <option value="mpatech">MPA/Tech</option>
                <option value="{{ \App\Models\People::TYPE_ACADEMIC }}">Academics</option>
            </select>
        </div>
        <div class="col">
            <label for="" class="form-label">Usergroup</label>
            <select class="form-select" aria-label="User group" wire:model="usergroup">
                <option value="">Any</option>
                @foreach ($usergroups as $group)
                    <option value="{{ $group }}">{{ $group }}</option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label for="" class="form-label">Supervisor</label>
            <select class="form-select" aria-label="User group" wire:model="supervisor">
                <option value="">Any</option>
                @foreach ($supervisors as $selectSupervisor)
                    <option value="{{ $selectSupervisor->id }}">{{ $selectSupervisor->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label for="" class="form-label">Building</label>
            <select class="form-select" aria-label="Building" wire:model="building">
                <option value="">Any</option>
                @foreach ($buildings as $selectBuilding)
                    <option value="{{ $selectBuilding->id }}">{{ $selectBuilding->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label for="" class="form-label" title="Select a building first">Room</label>
            <select class="form-select" aria-label="Building" wire:model="room">
                <option value="">Any</option>
                @if ($building != "")
                    @foreach ($rooms[$building] as $selectRoom)
                        <option value="{{ $selectRoom->id }}">{{ $selectRoom->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">Search for...</span>
                <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" wire:model="search">
            </div>
        </div>
    </div>
    <hr>
    <div class="d-flex justify-content-end">
        <button wire:click.prevent="exportCsv" class="btn btn-outline-primary">Export</button>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Surname</th>
                <th>Forenames</th>
                <th>Email</th>
                <th>Type</th>
                <th>Group</th>
                <th>Supervisor</th>
                <th>Started</th>
                <th>Ends</th>
                <th>Desks</th>
                <th>Lockers</th>
                <th>IT</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($people as $person)
                <tr>
                    <td>
                        <a href="{{ route('people.show', $person) }}">
                            {{ $person->surname }}
                        </a>
                    </td>
                    <td>{{ $person->forenames }}</td>
                    <td><a href="mailto:{{ $person->email }}">{{ $person->email }}</a></td>
                    <td>{{ $person->type }}</td>
                    <td>{{ $person->usergroup }}</td>
                    <td>
                        @if ($person->supervisor)
                            <a href="{{ route('reports.supervisor', $person->supervisor) }}">
                                {{ $person->supervisor->full_name }}
                            </a>
                        @endif
                    </td>
                    <td>{{ optional($person->start_at)->format('d/m/Y') }}</td>
                    <td>{{ optional($person->end_at)->format('d/m/Y') }}</td>
                    <td>{{ $person->desks_count }}</td>
                    <td>{{ $person->lockers_count }}</td>
                    <td>{{ $person->it_assets_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $people->links('pagination::bootstrap-4') }}
</div>
