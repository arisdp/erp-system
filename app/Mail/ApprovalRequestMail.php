<?php

namespace App\Mail;

use App\Models\ApprovalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApprovalRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $approvalRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(ApprovalRequest $approvalRequest)
    {
        $this->approvalRequest = $approvalRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $documentType = str_replace('App\\Models\\', '', $this->approvalRequest->approvable_type);
        return new Envelope(
            subject: "Approval Request: {$documentType} - " . ($this->approvalRequest->approvable->so_number ?? $this->approvalRequest->approvable->po_number ?? 'Document'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.approval_request',
            with: [
                'approval' => $this->approvalRequest,
                'document' => $this->approvalRequest->approvable,
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
