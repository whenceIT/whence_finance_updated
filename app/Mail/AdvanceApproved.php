<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Advance;

class AdvanceApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $advance;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Advance $advance)
    {
        $this->advance = $advance;
        //
    }


    public function build()
    {
        return $this->subject('Advance Approved')
                    ->view('emails.advance_approved');
    }
    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Advance Approved',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    
    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
