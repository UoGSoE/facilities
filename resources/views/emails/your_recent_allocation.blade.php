@component('mail::message')
# Recent Asset Allocation

This is an automated message to let you know about your recent asset allocation.

@foreach ($allocations as $allocation)
* {{ $allocation->getPrettyName() }} - {{ $allocation->room->building->name }} Room {{ $allocation->room->name }}
@endforeach

Thanks,<br>
{{ config('app.name') }}
@endcomponent
