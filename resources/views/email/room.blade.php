<x-layouts.app>
    @section('title') Email room {{ $room->name }} @endsection
    <h3>Send email to people in <a href="{{ route('building.show', $room->building) }}">{{ $room->building->name }}</a> room <a href="{{ route('room.show', $room) }}">{{ $room->name }}</a></h3>
    <hr>
    <form action="{{ route('email.room', $room) }}" method="POST">
        @csrf
        <label for="exampleFormControlInput1" class="form-label">Subject</label>
        <div class="mb-3 input-group">
            <span class="input-group-text" id="basic-addon1">{{ config('facilities.email_prefix') }}</span>
            <input type="text" class="form-control" id="exampleFormControlInput1" name="subject" value="{{ $room->building->name }} room {{ $room->name }}" required>
        </div>
        <div class="mb-3">
            <label for="exampleFormControlTextarea1" class="form-label">Message (you can use <a href="https://simplemde.com/markdown-guide" target="_blank">markdown</a> to format text)</label>
            <textarea class="form-control" id="exampleFormControlTextarea1" rows="8" name="message" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send</button>
    </form>
</x-layouts.app>
