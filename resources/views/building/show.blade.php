<x-layouts.app>
    <div class="d-flex justify-content-between">
        <div>
            <h3>Details for building {{ $building->name }}</h3>
        </div>
        <a href="{{ route('building.edit', $building) }}" class="btn btn-light">Edit</a>
    </div>
    <hr>
    <div class="d-flex justify-content-between">
        <div>
            <span class="bg-info text-white p-2">No Owner</span>
            <span class="bg-warning p-2">Leaving in < 28 days</span>
            <span class="bg-danger text-white p-2">Has Left</span>
        </div>
        <a class="btn btn-light" href="{{ route('room.create', $building) }}">Add a new room</a>
    </div>
    @foreach ($building->rooms as $room)
        @include('building.partials.desks_lockers')
    @endforeach
</x-layouts.app>
