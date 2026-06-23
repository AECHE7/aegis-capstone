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
        
        $mail = $this->subject("[A.E.G.I.S.] Official Update: Application {$status}")
                     ->view('emails.application_status');

        if ($this->application->status === 'Approved') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.application_form_pdf', ['application' => $this->application]);
            
            $mail->attachData($pdf->output(), "APP-{$this->application->id}_Approved_Form.pdf", [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}