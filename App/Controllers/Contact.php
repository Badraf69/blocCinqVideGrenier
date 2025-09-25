<?php
//
//namespace App\Controllers;
//
//use App\Models\Articles;
//use \Core\View;
//use Exception;
//
//class Contact extends \Core\Controller
//{
//    /**
//     * Affiche le formulaire de contact
//     */
//    public function formAction()
//    {
//        View::renderTemplate('Show/form.html');
//    }
//
//    /**
//     * Traite l’envoi du formulaire
//     */
//    public function sendAction()
//    {
//        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//            $to = "admin@bloc-cinq.local";
//            $subject = "Message depuis formulaire de contact";
//            $message = "Nom: " . $_POST['contact_name'] . "\nEmail: " . $_POST['contact_email'] . "\nMessage: " . $_POST['contact_message'];
//            $headers = "From: noreply@bloc-cinq.local";
//
//            if (mail($to, $subject, $message, $headers)) {
//                echo "Message envoyé !";
//            } else {
//                echo "Erreur lors de l'envoissss.";
//            }
//        }
//    }
//}
//


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

    public function sendAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mail = new PHPMailer(true);

            try {

                // SMTP Mailpit
                $mail->isSMTP();
                $mail->Host = 'mailpit';
                $mail->Port = 1025;
                $mail->SMTPAuth = false;
                $mail->SMTPSecure = ''; // pas false

                $mail->setFrom('noreply@bloc-cinq.local', 'Bloc-Cinq');
                $mail->addAddress('admin@bloc-cinq.local');

                $mail->Subject = 'Message depuis formulaire de contact';
                $mail->Body = "Nom: " . $_POST['contact_name'] .
                    "\nEmail: " . $_POST['contact_email'] .
                    "\nMessage: " . $_POST['contact_message'];

                $mail->send();
                View::renderTemplate('Home/index.html', []);
                exit;
                //echo '✅ Message envoyé !';

            } catch (Exception $e) {
                echo "❌ Erreur lors de l'envoi : {$mail->ErrorInfo}";
            }
        }
    }
}
