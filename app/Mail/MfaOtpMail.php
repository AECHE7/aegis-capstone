<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MfaOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from(config('mail.from.address', 'noreply@clsu-aegis.ph'), config('mail.from.name', 'AEGIS CLSU'))
                    ->subject('[A.E.G.I.S.] Verification Code for Login')
                    ->view('emails.mfa_otp');
    }
}
