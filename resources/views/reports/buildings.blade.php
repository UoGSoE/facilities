<x-layouts.app>
    @section('title') Buildings @endsection
    <h3>Building Report</h3>
    <hr>
    <table class="table">
        <thead>
            <tr>
                <th>Building</th>
                <th>Rooms</th>
                <th>Desks</th>
                <th>Used</th>
                <th>%</th>
                <th title="Allocated owner is leaving < 28 days">Free Soon</th>
                <th>Lockers</th>
                <th>Used</th>
                <th>%</th>
                <th title="Allocated owner is leaving < 28 days">Free Soon</th>
            </tr>
        </thead>
        <tbody>
            @foreach($buildings as $building)
                <tr>
                    <td><a href="{{ route('building.show', $building) }}">{{ $building->name }}</a></td>
                    <td>{{ $building->rooms->count() }}</td>
                    <td>{{ $building->desk_count }}</td>
                    <td>{{ $building->desk_used_count }}</td>
                    <td>{{ $building->desk_used_percent }}</td>
                    <td>{{ $building->desk_soon_count }}</td>
                    <td>{{ $building->locker_count }}</td>
                    <td>{{ $building->locker_used_count }}</td>
                    <td>{{ $building->locker_used_percent }}</td>
                    <td>{{ $building->locker_soon_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.app>
