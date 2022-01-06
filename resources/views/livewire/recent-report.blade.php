<div>
    <div class="row">
        <div class="col">
            <label for="" class="form-label">Allocated in the past</label>
            <div class="input-group">
                <input type="number" class="form-control" id="exampleFormControlInput1" placeholder="" wire:model="filterWeeks">
                <div class="input-group-text">weeks</div>
            </div>
        </div>
        <div class="col">
            <label for="" class="form-label">Person Type</label>
            <select class="form-select" aria-label="Type of Person" wire:model="peopleType">
                <option value="any">Any</option>
                @foreach (app('people.types') as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label for="" class="form-label">Asset Type</label>
            <select class="form-select" aria-label="Type of Asset" wire:model="assetType">
                <option value="any">Any</option>
                <option value="desk">Desk</option>
                <option value="locker">Locker</option>
            </select>
        </div>
    </div>
    <hr>
    <div class="d-flex justify-content-between">
        <div>
        <button wire:click.prevent="sendEmail" @if (count($mailToIds) == 0 || session()->has('emailMessage')) disabled @endif class="btn btn-outline-success me-2">
            @if (session()->has('emailMessage'))
                {{ session('emailMessage') }}
            @else
                Email Their Allocations
            @endif
        </button>
        </div>

        <div>
        <button wire:click.prevent="exportCsv" class="btn btn-outline-primary">Export</button>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th><i class="bi bi-envelope"></i></th>
                <th>Person</th>
                <th>Type</th>
                <th>Asset</th>
                <th>Building</th>
                <th>Room</th>
                <th>Allocated</th>
                <th>Avanti</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($assets as $asset)
                <tr>
                    <td>
                        <input type="checkbox" wire:model="mailToIds" value="{{ $asset->owner->id }}">
                    </td>
                    <td>
                        <a href="{{ route('people.show', $asset->owner) }}">
                            {{ $asset->owner->full_name }}
                        </a>
                    </td>
                    <td>{{ $asset->owner->type }}</td>
                    <td>{{ $asset->getPrettyName() }}</td>
                    <td>
                        <a href="{{ route('building.show', $asset->room->building) }}">
                            {{ $asset->room->building->name }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('room.show', $asset->room) }}">
                            {{ $asset->room->name }}
                        </a>
                    </td>
                    <td>{{ $asset->allocated_at->format('d/m/Y') }}</td>
                    <td>{{ $asset->avanti_ticket_id }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">
                    {{ $assets->count() }} Total
                </td>
            </tr>
        </tfoot>
    </table>
</div>
