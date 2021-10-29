<x-layouts.app>
    <h3>Add a new building</h3>
    <form action="{{ route('building.store') }}" method="post">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        @error('name')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        <div class="mb-3">
            <button class="btn btn-secondary">Save</button>
        </div>
    </form>
</x-layouts.app>
