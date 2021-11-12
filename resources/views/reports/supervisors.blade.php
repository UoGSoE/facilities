<x-layouts.app>
    @section('title') Supervisors @endsection
    <h3>Supervisors Report</h3>
    <hr>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Supervising</th>
                <th>Desks</th>
                <th>Lockers</th>
                <th>IT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supervisors as $supervisor)
                <tr>
                    <td><a href="{{ route('reports.supervisor', $supervisor) }}">{{ $supervisor->full_name }}</a></td>
                    <td><a href="mailto:{{ $supervisor->email }}">{{ $supervisor->email }}</a></td>
                    <td>
                        {{ $supervisor->supervisees->count() }}
                    </td>
                    <td>{{ $supervisor->desk_count }}</td>
                    <td>{{ $supervisor->locker_count }}</td>
                    <td>{{ $supervisor->it_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.app>
