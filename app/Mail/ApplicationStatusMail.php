<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;

class ApplicationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function build()
    {
        $status = strtoupper($this->application->status);
        
        return $this->subject("[A.E.G.I.S.] Official Update: Application {$status}")
                    ->view('emails.application_status');
    }
}