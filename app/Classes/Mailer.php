<?php

namespace App\Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    /**
     * Method to send emails
     *
     * The send method requires these parameters [view, subject, recipients[] ,cc[] (o), bcc[] (o),
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
    public static function send(object $request)
    {
        try {

            $config = (object)config('mail.mailers.smtp');

            $phpMailer = new PHPMailer(true);
            $phpMailer->SMTPDebug = $config->debug;
            $phpMailer->isSMTP();
            $phpMailer->Host = $config->host;
            $phpMailer->SMTPAuth = $config->auth;
            $phpMailer->Username = $config->username;
            $phpMailer->Password = $config->password;
            $phpMailer->SMTPSecure = $config->encryption;
            $phpMailer->Port = $config->port;
            $phpMailer->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));

            if (!view()->exists($request->view))
                throw new Exception('View doesn\'t exists');

            foreach ($request->recipients as $recipient)
                $phpMailer->addAddress($recipient);

            if (isset($request->cc)) {
                foreach ($request->cc as $cc)
                    $phpMailer->addCC($cc);
            }

            if (isset($request->bcc)) {
                foreach ($request->bcc as $bcc)
                    $phpMailer->addCC($bcc);
            }

            if (isset($request->replys)) {
                foreach ($request->replys as $reply)
                    $phpMailer->addReplyTo($reply['email'], $reply['name']);
            }

            if (isset($request->attachments)) {
                foreach ($request->attachments as $attachment)
                    $phpMailer->addAttachment($attachment['path'], $attachment['name']);
            }

            $phpMailer->Subject = $request->subject;
            $phpMailer->Body = view($request->view, $request->view_data)->render();
            $phpMailer->IsHTML(true);

            if (!$phpMailer->send())
                throw new Exception('Email not sent');

        } catch (Exception $e) {
            throw new Exception('Message could not be sent: ' . $e->getMessage());
        }
    }
}
