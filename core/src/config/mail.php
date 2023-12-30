<?php
/**
 * Created by PhpStorm.
 * Author: Samtax01
 * Date: 08/07/2018
 * Time: 7:47 AM
 * @link https://packagist.org/packages/phpmailer/phpmailer
 */


    /************************************************
     *  PHP Mailer
     ************************************************/
    /**
     * If using gmail, please allow less secure app @link https://myaccount.google.com/lesssecureapps
     * @return \PHPMailer\PHPMailer\PHPMailer
     * @param bool $exception
     *
    * //Recipients
    * $mail->setFrom('from@example.com', 'Mailer');
    * $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
    * $mail->addAddress('ellen@example.com');               // Name is optional
    * $mail->addReplyTo('info@example.com', 'Information');
    * $mail->addCC('cc@example.com');
    * $mail->addBCC('bcc@example.com');
    *
    * //Attachments
    * $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    * $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    *
    * //Content
    * $mail->isHTML(true);                                  // Set email format to HTML
    * $mail->Subject = 'Here is the subject';
    * $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    * $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    *
    * $mail->send();
    */
    function mailer_config($exception = false)
    {
        $mail = new PHPMailer\PHPMailer\PHPMailer($exception);
        $mail->SMTPDebug = $exception? 2: 0;
        $mail->isSMTP();
        $mail->Host = env('MAIL_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = env('MAIL_EMAIL');
        $mail->Password = env('MAIL_PASSWORD');
        $mail->SMTPSecure = env('MAIL_SMTP_ENCRYPTION', 'ssl');
        $mail->Port = env('MAIL_PORT');
        return $mail;
    }


    function mailer_send_mail_to_list(array $toUserEmail_and_UserName_keyValue, $subject, $htmlMessageContent, $attachmentPath = null, $fromEmail = null, $fromUserName_orCompanyName = null, $exception = false, $mailer_config_instance = null){
        $mail = $mailer_config_instance? $mailer_config_instance: mailer_config($exception);


        try {
            $result = '';
            ob_start();
                //Recipients
                $noReplyMail = explode('//', Url1::getDomainName())[1];
                $noReplyMail = 'no-reply'.'@'.(String1::contains('.', $noReplyMail)? $noReplyMail: $noReplyMail.'.com');
                $mail->setFrom(($fromEmail? $fromEmail: $noReplyMail), ($fromUserName_orCompanyName? $fromUserName_orCompanyName: env('APP_TITLE')));
                $mail->addReplyTo(($fromEmail? $fromEmail: $noReplyMail), ($fromUserName_orCompanyName? $fromUserName_orCompanyName: env('APP_TITLE')));

                foreach ($toUserEmail_and_UserName_keyValue as $email=>$userName) {
                    $email1 = is_numeric($email)? $userName: $email;
                    $userName1 = is_numeric($email)? Form1::extractUserName($userName, false): $userName;
                    $mail->addAddress($email1, $userName1);     // Add a recipient
                }

                //Attachments
                if($attachmentPath) $mail->addAttachment($attachmentPath, 'Attachment File');

                //Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $htmlMessageContent;
                $mail->AltBody = Html1::removeTag($htmlMessageContent);

                $status = $mail->send();
                $mail->ClearAddresses();
                if(!$status) return ResultStatus1::falseMessage('Message Sending Failed!, Due to Error: '.$mail->ErrorInfo);
            $result = ob_get_contents();
            ob_end_clean();

            if($exception && $result) {
                dd( $result, 'Mail Status' );
            }
            return ResultStatus1::make($status,$status? 'Message has been sent': 'Message could not be sent. Error is : '.$mail->ErrorInfo, null);
        } catch (Exception $e) {
            if($exception) {
                dd($e);
            }
            return ResultStatus1::falseMessage('Message could not be sent. Error is : '.$mail->ErrorInfo);
        }
    }


