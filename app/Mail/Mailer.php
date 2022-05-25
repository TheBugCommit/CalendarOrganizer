<?php
/**
 * Author: Gerard Casas
 * Date: 25/05/2022
 * Allows sending emails
 */

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Mail;

class Mailer
{
    private $recipient;
    private $mailableClass;
    private $data;

    /**
     *  Constructor
     *
     * @param array    $recipient      Associative array that should contain the recipient name and email
     * @param string   $mailableClass  Name of the class that is responsible for sending the mail
     * @param array    $data           Associative array with the data that will be sent to the email view
     */

    function __construct($recipient, $mailableClass, $data)
    {
        $this->setRecipient($recipient);
        $this->setMailableClass($mailableClass);
        $this->setData($data);
    }

    private function getRecipient()
    {
        return $this->recipient;
    }

    private function getMailableClass()
    {
        return $this->mailableClass;
    }

    private function getData()
    {
        return $this->data;
    }

    private function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    private function setMailableClass($mailableClass)
    {
        $this->mailableClass = $mailableClass;
    }

    private function setData($data)
    {
        $this->data = $data;
    }

    /**
     *  Method that sends the email
     *
     *  @param None
     */
    public function send()
    {
        $to = new User();
        $user = $this->getRecipient();

        $to->name = $user['name'];
        $to->email = $user['email'];

        $class = $this->getMailableClass();
        $mail = Mail::mailer('smtp')->to($to);
        $mail->send(new $class($this->getData()));
    }
}
