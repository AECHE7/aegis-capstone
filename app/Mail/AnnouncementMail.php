<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use Illuminate\Contracts\Queue\ShouldQueue;

class AnnouncementMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $title;
    public $content;

    /**
     * Create a new message instance.
     */
    public function __construct(string $title, string $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("[A.E.G.I.S.] Official Announcement: " . $this->title)
                    ->view('emails.announcement');
    }
}
