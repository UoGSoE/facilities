<x-layouts.app>
    @section('title') {{ $room->building->name }} Room {{ $room->name }} @endsection
    <div class="d-flex justify-content-between">
        <div>
            <h3>Details for {{ $room->building->name }} room {{ $room->name }}
        @livewire('notes-editor', ['model' => $room])

            </h3>
        </div>
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
