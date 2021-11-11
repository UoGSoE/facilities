<x-layouts.app>
    <div class="d-flex justify-content-between">
        <div>
            <h3>
                Details for building {{ $building->name }}
                <a href="{{ route('email.building_form', $building) }}" class="btn btn-light btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                    </svg>
                </a>

            </h3>
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
