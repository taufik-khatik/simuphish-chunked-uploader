<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UploadSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $uploadUrl, $fileName;

    /**
     * Create a new message instance.
     */
    public function __construct($uploadUrl, $fileName)
    {
        $this->uploadUrl = $uploadUrl;
        $this->fileName = $fileName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Simuphish - Chunked Upload Success Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.upload_success',
        );
    }

    /**
     * Get the attachments (none here).
     */
    public function attachments(): array
    {
        return [];
    }
}
