<x-layouts.app>
    @section('title') Import new requests @endsection
    <h3>Import new requests</h3>
    <hr>

    <label class="form-label">Format</label>
    <pre>Request ID,Person,Email,PGR start date,Staff start date,PGR+STAFF START DATE,Summary,Request Type,Description,Created On,Hour Logged,Last Modified,Team,Owner,Status,Location</pre>

    <form action="{{ route('import.new_requests') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">Excel sheet</label>
            <input type="file" class="form-control" id="file" name="sheet" required>
        </div>
        <button type="submit" class="btn btn-primary">Import</button>
    </form>
</x-layouts.app>
