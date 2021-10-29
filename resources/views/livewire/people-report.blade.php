<div>
    <div class="row">
        <div class="col">
            <label for="" class="form-label">Leaving in the next</label>
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
            <label for="" class="form-label">Search</label>
            <div class="input-group">
                <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="" wire:model="search">
            </div>
        </div>
    </div>
    <form>
    </form>
    <hr>
    <table class="table">
        <thead>
            <tr>
                <th>Surname</th>
                <th>Forenames</th>
                <th>Email</th>
                <th>Type</th>
                <th>Supervisor</th>
                <th>Started</th>
                <th>Ends</th>
                <th>Desks/Lockers</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($people as $person)
                <tr>
                    <td>{{ $person->surname }}</td>
                    <td>{{ $person->forenames }}</td>
                    <td><a href="mailto:{{ $person->email }}">{{ $person->email }}</a></td>
                    <td>{{ $person->type }}</td>
                    <td>{{ optional($person->supervisor)->full_name }}</td>
                    <td>{{ $person->start_at->format('d/m/Y') }}</td>
                    <td>{{ $person->end_at->format('d/m/Y') }}</td>
                    <td>{{ $person->desks_count }} / {{ $person->lockers_count }} </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $people->links('pagination::bootstrap-4') }}
</div>
