@component('mail::message')
# People who are arriving or leaving in the next {{ config('facilities.email_alert_days') }} days

## Arrivals ({{ $arrivals->count() }})

@component('mail::table')
| Name       | Email         | Type  | Date|
|:------------- |:------------- |:-------- |:--------- |
@foreach ($arrivals as $person)
| {{ $person->full_name }} | {{ $person->email }} | {{ $person->type }} | {{ $person->start_at->format('d/m/Y') }} |
@endforeach
@endcomponent

## Departures ({{ $departures->count() }})

@component('mail::table')
| Name       | Email         | Type  | Date|
|:------------- |:------------- |:-------- |:--------- |
@foreach ($departures as $person)
| {{ $person->full_name }} | {{ $person->email }} | {{ $person->type }} | {{ $person->end_at->format('d/m/Y') }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => route('home')])
Facilities DB
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
