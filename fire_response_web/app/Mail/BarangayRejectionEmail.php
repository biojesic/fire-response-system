<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BarangayRejectionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $barangayName;
    public $rejectionReason;
    public $canReapply;

    /**
     * Create a new message instance.
     */
    public function __construct(
        $barangayName, 
        $rejectionReason, 
        $canReapply,
        $token // New parameter
    ) {
        $this->barangayName = $barangayName;
        $this->rejectionReason = $rejectionReason;
        $this->canReapply = $canReapply;
        $this->token = $token; // Store token
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Barangay Application Rejected',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.barangay_rejected',
            with: [
                'barangayName' => $this->barangayName,
                'rejectionReason' => $this->rejectionReason,
                'reapplyUrl' => $this->canReapply 
                    ? route('barangay.reapply', ['token' => $this->token])
                    : null
            ],
        );
    }
    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}