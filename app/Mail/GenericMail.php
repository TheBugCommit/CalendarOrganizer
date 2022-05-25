<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return GenericMail $this
     */
    public function build()
    {
        $subject = $this->data['subject'];

        $mail = $this->subject($subject)
            ->view($this->data['view'], [$this->data['varname'] => $this->data['data']]);

        if (isset($this->data['attach_file'])) {
            $mail->attach($this->data['attach_file']['path'], [
                'as'   => $this->data['attach_file']['name'],
                'mime' => $this->data['attach_file']['mime']
            ]);
        }

        return $mail;
    }
}
