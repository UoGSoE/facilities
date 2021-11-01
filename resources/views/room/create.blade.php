<x-layouts.app>
    <h3>Add a new room to <a href="{{ route('building.show', $room->building) }}">{{ $room->building->name }}</a></h3>
    <hr>
    <form action="{{ route('room.store', $building->id) }}" method="post" class="row row-cols-lg-auto g-3 align-items-center d-flex justify-content-between">
        @csrf
        <div class="col-12">
            <div class="input-group">
                <div class="input-group-text">Name</div>
                <input type="text" class="form-control" id="name" name="name" value="{{ $room->name }}" required>
            </div>
            @error('name')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <div class="input-group">
                <label for="desks" class="visually-hidden">No. Desks</label>
                <div class="input-group-text">Desks</div>
                <input type="number" class="form-control" id="desks" name="desks" value="{{ $room->desks->count() }}" min="0">
            </div>
            @error('desks')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <div class="input-group">
                <label for="lockers" class="visually-hidden">No. Lockers</label>
                <div class="input-group-text">Lockers</div>
                <input type="number" class="form-control" id="lockers" name="lockers" value="{{ $room->lockers->count() }}" min="0">
            </div>
            @error('lockers')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-12">
            <div class="input-group">
                <button class="btn btn-secondary">Save</button>
            </div>
        </div>
    </form>
</x-layouts.app>
