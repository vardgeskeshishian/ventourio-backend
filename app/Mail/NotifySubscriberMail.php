<?php

namespace App\Mail;

use App\Models\System\HasSubscribers;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifySubscriberMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public HasSubscribers $model) {}

    /**
     * Get the message envelope.
     *
     * @return Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->model->getMailSubject(),
        );
    }

    public function build(): Mailable
    {
        return $this->markdown($this->model->getMailTemplate(), $this->model->getMailData());
    }
}
