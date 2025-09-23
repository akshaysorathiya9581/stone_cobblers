@component('mail::message')
# Hello {{ $customer->name }},

We just contacted you on **{{ $customer->last_contact->toDayDateTimeString() }}**.  
Thank you for being our valued customer!

@component('mail::button', ['url' => url('/')])
Visit Website
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
