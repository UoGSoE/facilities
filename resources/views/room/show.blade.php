<x-layouts.app>
    <div class="d-flex justify-content-between">
        <div>
            <h3>Details for {{ $room->building->name }} room {{ $room->name }}</h3>
        </div>
        <a href="{{ route('room.edit', $room) }}" class="btn btn-light">Edit</a>
    </div>
    <hr>
    <div class="d-flex justify-content-between">
        <div>
            <span class="bg-info text-white p-2">No Owner</span>
            <span class="bg-warning p-2">Leaving in < 28 days</span>
            <span class="bg-danger text-white p-2">Has Left</span>
        </div>
    </div>
    @include('building.partials.desks_lockers')
</x-layouts.app>
