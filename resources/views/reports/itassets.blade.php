<x-layouts.app>
    <h3>IT Assets Report</h3>
    <hr>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Started</th>
                <th>Ends</th>
                <th>
                    Assets
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($people as $person)
                <tr @if ($person->isLeavingSoon()) class="table-warning" @endif>
                    <td>{{ $person->full_name }}</td>
                    <td>{{ $person->email }}</td>
                    <td>{{ $person->start_at->format('d/m/Y') }}</td>
                    <td>{{ $person->end_at->format('d/m/Y') }}</td>
                    <td>
                        {{ $person->itAssets->map(fn ($asset) => $asset->asset_number . ' ' . $asset->name)->implode(', ') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-layouts.app>
