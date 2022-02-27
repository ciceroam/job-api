<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class EmailController extends Controller
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        //Server settings
        $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;              //Enable verbose debug output
        $this->mail->isSMTP();                                    //Send using SMTP
        $this->mail->SMTPAuth = true;                             //Enable SMTP authentication
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //Enable TLS encryption
        $this->mail->Port = 587;                                  //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        $this->mail->Encoding = PHPMailer::ENCODING_8BIT;         // Email uses 8-bit encoding
        $this->mail->Host = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $this->mail->Username = 'ciceroaam@example.com';          //SMTP username
        $this->mail->Password = 'Caam0007';                       //SMTP password
    }

    /**
     * Send email to a cellphone.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request)
    {
        try {
            //Recipients
            $this->mail->setFrom('from@example.com', 'Mailer');
            $this->mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
            $this->mail->addAddress('ellen@example.com');               //Name is optional
            $this->mail->addReplyTo('info@example.com', 'Information');
            $this->mail->addCC('cc@example.com');
            $this->mail->addBCC('bcc@example.com');

            //Attachments
            $this->mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            $this->mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $this->mail->isHTML(true);                                  //Set email format to HTML
            $this->mail->Subject = 'Here is the subject';
            $this->mail->Body = 'This is the HTML message body <b>in bold!</b>';
            $this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $this->mail->send();

            return response()->json('Message send!');
        } catch (Exception $e) {
            abort(500, $e->errorMessage());
        }
    }

}
