<x-layouts.app>
    <div class="row">
        <div class="col">
            <p>Buildings</p>
            <h5>{{ $buildings->count() }}</h5>
        </div>
        <div class="col">
            <p>Rooms</p>
            <h5>{{ $totals['room_counts'] }}</h5>
        </div>
        <div class="col">
            <p>Desks</p>
            <h5>{{ $totals['desk_count'] }} ({{ $totals['desk_used_count'] }} used)</h5>
        </div>
        <div class="col">
            <p>Lockers</p>
            <h5>{{ $totals['locker_count'] }} ({{ $totals['locker_used_count'] }} used)</h5>
        </div>
        <div class="col">
            <p>People</p>
            <h5>{{ $activePeopleCount }}</h5>
        </div>
    </div>
    <hr>
    <div class="d-flex flex-row-reverse">
        <a href="{{ route('building.create') }}" class="btn btn-light mb-4">Add a new building</a>
    </div>
    @foreach ($buildings->chunk(3) as $someBuildings)
    <div class="row mb-4">
        @foreach ($someBuildings as $building)
            <div class="col">
              <div class="card" style="width: 18rem;">
                <div class="card-body">
                  <h5 class="card-title bg-secondary text-white p-4">{{ $building->name }}</h5>
                  <p class="card-text">
                      <ul class="list-group">
                        <li class="list-group-item">Rooms: {{ $building->rooms->count() }}</li>
                        <li class="list-group-item">Desks: {{ $building->desk_used_count }} / {{ $building->desk_count }}</li>
                        <li class="list-group-item">Lockers: {{ $building->locker_used_count }} / {{ $building->locker_count }}</li>
                    </ul>
                  </p>
                  <a href="{{ route('building.show', $building->id) }}" class="btn btn-light w-100">More...</a>
                </div>
              </div>
            </div>
        @endforeach
    </div>
    @endforeach
</x-layouts.app>
