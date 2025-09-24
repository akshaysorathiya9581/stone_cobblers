<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Quote;

class QuoteStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quote;
    public $status; // 'Approved' or 'Rejected'

    public function __construct(Quote $quote, string $status)
    {
        $this->quote = $quote;
        $this->status = $status;
    }

    public function build()
    {
        $subject = "Quote #{$this->quote->quote_number} - {$this->status}";
        return $this->subject($subject)
                    ->markdown('emails.quotes.status_changed');
    }
}
