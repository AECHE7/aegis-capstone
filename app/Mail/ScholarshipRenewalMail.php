<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use Illuminate\Contracts\Queue\ShouldQueue;
use Barryvdh\DomPDF\Facade\Pdf;

class ScholarshipRenewalMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Application $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function build(): self
    {
        $mail = $this->subject("[A.E.G.I.S.] Scholarship Grant Successfully Renewed: {$this->application->program_name}")
                     ->view('emails.scholarship_renewal');

        try {
            $pdf = Pdf::loadView('emails.application_form_pdf', ['application' => $this->application]);
            $mail->attachData($pdf->output(), "APP-{$this->application->id}_Renewal_Evaluation_Form.pdf", [
                'mime' => 'application/pdf',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Could not attach PDF to renewal email for APP-{$this->application->id}: " . $e->getMessage());
        }

        return $mail;
    }
}
