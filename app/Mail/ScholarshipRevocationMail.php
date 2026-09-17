<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Application;
use Illuminate\Contracts\Queue\ShouldQueue;

class ScholarshipRevocationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Application $application;
    public string $reason;

    public function __construct(Application $application, string $reason)
    {
        $this->application = $application;
        $this->reason = $reason;
    }

    public function build(): self
    {
        return $this->subject("[A.E.G.I.S.] Official Notice: Scholarship Grant Revocation ({$this->application->program_name})")
                    ->view('emails.scholarship_revocation');
    }
}
