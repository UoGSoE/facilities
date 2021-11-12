@component('mail::message')
# New Asset Allocations

The following assets have just been allocated:

@component('mail::table')
| Person                 | Asset                 | Room                 | Building                 | Avanti                 |
|:---------------------- |:--------------------- |:-------------------- |:------------------------ |:---------------------- |
@foreach ($assets as $asset)
| {{ $asset['person'] }} | {{ $asset['asset'] }} | {{ $asset['room'] }} | {{ $asset['building'] }} | {{ $asset['avanti'] }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => route('reports.recent')])
See all recent allocations
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
