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
        $companyName = setting('company_name', 'Stone Cobblers Inc.');
        $fromEmail = setting('email_from_address', 'noreply@stonecobblers.com');
        $fromName = setting('email_from_name', $companyName);
        
        $subject = "Your Quote #{$this->quote->quote_number} from {$companyName}";
        
        return $this->from($fromEmail, $fromName)
                    ->subject($subject)
                    ->markdown('emails.quotes.sent')
                    ->with([
                        'companyName' => $companyName,
                        'companyPhone' => setting('company_phone', '(555) 123-4567'),
                        'companyEmail' => setting('company_email', 'info@stonecobblers.com'),
                    ]);
    }
}
