@component('mail::message')
# Hello {{ optional($quote->project->customer)->name ?? 'Customer' }},

This is to inform you that **Quote #{{ $quote->quote_number }}** has been **{{ $status }}**.

@if($status === 'Approved')
We will proceed with the next steps. If you have any questions, reply to this email.
@else
If you want us to revise the quote or discuss alternatives, please reply and we'll get back to you.
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
