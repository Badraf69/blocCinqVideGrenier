<?php
namespace App\Controllers;

use \Core\View;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Contact extends \Core\Controller
{
    public function formAction()
    {
        View::renderTemplate('Show/form.html');
    }
    protected function createMailer():PHPMailer{
        return new PHPMailer(true);
    }
    public function sendAction()
    {
        global $_test;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail = $this->createMailer();

            try {
                // SMTP Mailpit
                $mail->isSMTP();
                $mail->Host = 'mailpit';
                $mail->Port = 1025;
                $mail->SMTPAuth = false;
                $mail->SMTPSecure = '';
                $mail->setFrom('noreply@bloc-cinq.local', 'Bloc-Cinq');
                $mail->addAddress('admin@bloc-cinq.local');
                $mail->Subject = 'Message depuis formulaire de contact';
                $mail->Body = "Nom: " . $_POST['contact_name'] .
                    "\nEmail: " . $_POST['contact_email'] .
                    "\nMessage: " . $_POST['contact_message'];
                $mail->send();
                if (!$_test){
                    View::renderTemplate('Home/index.html', []);
                }
                else {
                echo '✅ Message envoyé !';
                }
                exit;


            } catch (Exception $e) {
                echo "❌ Erreur lors de l'envoi : {$mail->ErrorInfo}";
            }
        }
    }
}
