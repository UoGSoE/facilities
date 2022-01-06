<x-layouts.app>
    <h3>Reallocate everyone from {{ $room->building->name }} room {{ $room->name }}</h3>
    <p>
        Reallocation is done on a 'best effort' basis. If a person is not reallocated in the way chosen (ie, not enough desks in the new
        building), they will be allocated in another one.
    </p>
    <form method="POST" action="{{ route('room.do_reallocate', $room) }}">
        @csrf
        <div class="input-group">
            <select class="form-select" id="reallocate_to" multiple name="reallocate_to[]" aria-label="Reallocate all users to new room" required>
                <option value="-1" selected>Anywhere</option>
                @foreach ($buildings as $building)
                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                @endforeach
            </select>
        </div>
        <hr>
        <div class="input-group">
            <button class="btn btn-primary" id="reallocate_button" type="submit">Reallocate</button>
        </div>
    </form>
</x-layouts.app>
