<x-layouts.app>
    <h3>Send email to people in <a href="{{ route('building.show', $building) }}">{{ $building->name }}</a></h3>
    <hr>
    <form action="{{ route('email.building', $building) }}" method="POST">
        @csrf
        <label for="exampleFormControlInput1" class="form-label">Subject</label>
        <div class="mb-3 input-group">
            <span class="input-group-text" id="basic-addon1">{{ config('facilities.email_prefix') }}</span>
            <input type="text" class="form-control" id="exampleFormControlInput1" name="subject" value="{{ $building->name }}" required>
        </div>
        <div class="mb-3">
            <label for="exampleFormControlTextarea1" class="form-label">Message (you can use <a href="https://simplemde.com/markdown-guide" target="_blank">markdown</a> to format text)</label>
            <textarea class="form-control" id="exampleFormControlTextarea1" rows="8" name="message" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send</button>
    </form>
</x-layouts.app>
