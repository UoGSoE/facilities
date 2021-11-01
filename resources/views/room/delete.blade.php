<x-layouts.app>
    <h3>
        Really delete room {{ $room->name }} in {{ $room->building->name }}?

    </h3>
    <p>
        This will remove all the associated desks and lockers and <b>cannot be undone</b>.
    </p>
    <form action="{{ route('room.destroy', $room->id) }}" method="post">
        @csrf
        <div class="input-group">
            <a href="{{ route('building.show', $room->building) }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-danger">Yes, Really Delete Room {{ $room->name }}</button>
        </div>
    </form>
</x-layouts.app>
