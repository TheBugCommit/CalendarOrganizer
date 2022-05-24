<?php

namespace App\Http\Controllers;

use App\Http\Requests\MailSendRequest;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerController extends Controller
{

    /**
     * @var PHPMailer $phpMailer        handle PHPMailer instance
     */
    protected $phpMailer;

    /**
     * Initialize PHPMailer instance
     */
    protected function __construct()
    {
        $config = (object)config('mail.mailers.smtp');

        $this->phpMailer = new PHPMailer(true);
        $this->phpMailer->SMTPDebug = $config->debug;
        $this->phpMailer->isSMTP();
        $this->phpMailer->Host = $config->host;
        $this->phpMailer->SMTPAuth = $config->auth;
        $this->phpMailer->Username = $config->username;
        $this->phpMailer->Password = $config->password;
        $this->phpMailer->SMTPSecure = $config->encryption;
        $this->phpMailer->Port = $config->port;
        $this->phpMailer->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
    }

    /**
     * Method to send emails
     *
     * The send method requires these parameters [view, subject, body, recipients[] ,cc[] (o), bcc[] (o),
     *  attachments[] (o), replys[] (o), view_data (o)],
     * where those marked with (o) are optional.
     *
     * - The attachments must be in the storage directory
     * - Attachments format must be ['path' => '...', 'name' => '...']
     * - Replys format must be ['email' => '...', 'name' => '...']
     *
     * To configure email host, auth ... edit .env and config/mail.php
     *
     * @param Request $request
     * @return ResponseJson
     */
    public function send(MailSendRequest $request)
    {

        try {
            if(!view()->exists($request->view))
                throw new Exception('View doesn\'t exists');

            foreach ($request->recipients as $recipient)
                $this->phpMailer->addAddress($recipient);

            if (isset($request->cc)) {
                foreach ($request->cc as $cc)
                    $this->phpMailer->addCC($cc);
            }

            if (isset($request->bcc)) {
                foreach ($request->bcc as $bcc)
                    $this->phpMailer->addCC($bcc);
            }

            if (isset($request->replys)) {
                foreach ($request->replys as $reply)
                    $this->phpMailer->addReplyTo($reply['email'], $reply['name']);
            }

            if (isset($request->attachments)) {
                foreach ($request->attachments as $attachment)
                    $this->phpMailer->addAttachment($attachment['path'], $attachment['name']);
            }

            $this->phpMailer->Subject = $request->subject;
            $this->phpMailer->Body = view($request->view, $request->view_data)->render();
            $this->phpMailer->IsHTML(true);

            if (!$this->phpMailer->send())
                throw new Exception('Email not sent');

        } catch (Exception $e) {
            return response()->json(['message' => 'Message could not be sent.'], 500);
        }

        return response()->json(['message' => 'Email sent']);
    }
}
