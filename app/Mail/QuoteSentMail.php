<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Quote;

class QuoteSentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quote;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    public function build()
    {
        $subject = "Your Quote #{$this->quote->quote_number}";
        return $this->subject($subject)
                    ->markdown('emails.quotes.sent');
    }
}
