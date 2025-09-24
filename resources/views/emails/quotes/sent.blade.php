@component('mail::message')
# Hello {{ optional($quote->project->customer)->name ?? 'Customer' }},

We have sent you **Quote #{{ $quote->quote_number }}** with a total amount of **${{ number_format($quote->total,2) }}**.

@if($quote->pdf_path)
You can download the PDF using the button below.
@component('mail::button', ['url' => url(route('admin.quotes.download', $quote->id))])
Download Quote
@endcomponent
@endif

If you have any questions, reply to this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
