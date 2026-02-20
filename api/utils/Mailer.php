<?php
// api/utils/Mailer.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../lib/PHPMailer/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';

class Mailer
{
    private $host;
    private $port;
    private $username;
    private $password;

    public function __construct()
    {
        $this->loadEnv();
    }

    private function loadEnv()
    {
        $envPath = __DIR__ . '/../../.env';
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    list($name, $value) = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim($value);
                    if ($name === 'SMTP_HOST') $this->host = $value;
                    if ($name === 'SMTP_PORT') $this->port = $value;
                    if ($name === 'SMTP_USER') $this->username = $value;
                    if ($name === 'SMTP_PASS') $this->password = $value;
                }
            }
        }
    }

    public function sendConfirmation($to_email, $to_name, $lead_details)
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = $this->host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->username;
            $mail->Password   = $this->password;
            
            if ($this->port == 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->Port       = $this->port;

            // Bypass SSL verification (often needed for local/dev envs or self-signed certs)
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->Timeout = 5; // Timeout checking after 5 seconds
            $mail->Timelimit = 5; // Limit execution time

            //Recipients
            $mail->setFrom($this->username, 'Rappelez-moi.co');
            $mail->addAddress($to_email, $to_name);

            //Content
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Confirmation de votre demande de rappel';
            
            $body = "<h1>Bonjour $to_name,</h1>";
            $body .= "<p>Nous avons bien reçu votre demande de rappel.</p>";
            $body .= "<p><strong>Détails :</strong></p>";
            $body .= "<ul>";
            $body .= "<li>Besoin : " . htmlspecialchars($lead_details['need'] ?? '') . "</li>";
            $body .= "<li>Créneau souhaité : " . htmlspecialchars($lead_details['time_slot'] ?? 'Non spécifié') . "</li>";
            $body .= "<li>Téléphone : " . htmlspecialchars($lead_details['phone'] ?? '') . "</li>";
            $body .= "</ul>";
            $body .= "<p>Un de nos conseillers vous contactera bientôt.</p>";
            $body .= "<p>Cordialement,<br>L'équipe Rappelez-moi.co</p>";

            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            return true;
        } catch (Exception $e) {
            $errorMsg = $mail->ErrorInfo;
            error_log("Mailer Error: " . $errorMsg);
            return false;
        }
    }

    public function sendActivationEmail($to_email, $to_name, $verification_code)
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = $this->host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->username;
            $mail->Password   = $this->password;
            
            if ($this->port == 465) {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->Port       = $this->port;

            // Bypass SSL verification
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            //Recipients
            $mail->setFrom($this->username, 'Rappelez-moi.co');
            $mail->addAddress($to_email, $to_name);

            //Content
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Activez votre compte Rappelez-moi.co';
            
            $body = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>";
            $body .= "<h1 style='color: #333;'>Bonjour $to_name,</h1>";
            $body .= "<p style='font-size: 16px; color: #555;'>Bienvenue sur <strong>Rappelez-moi.co</strong> !</p>";
            $body .= "<p style='font-size: 16px; color: #555;'>Pour activer votre compte, veuillez utiliser le code de vérification suivant :</p>";
            $body .= "<div style='background-color: #f4f4f4; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px;'>";
            $body .= "<h2 style='color: #4CAF50; font-size: 32px; letter-spacing: 8px; margin: 0;'>$verification_code</h2>";
            $body .= "</div>";
            $body .= "<p style='font-size: 14px; color: #777;'>Ce code est valide pour votre activation de compte.</p>";
            $body .= "<p style='font-size: 14px; color: #777;'>Si vous n'avez pas créé de compte, veuillez ignorer cet email.</p>";
            $body .= "<hr style='border: none; border-top: 1px solid #eee; margin: 30px 0;'>";
            $body .= "<p style='font-size: 14px; color: #999;'>Cordialement,<br>L'équipe Rappelez-moi.co</p>";
            $body .= "</div>";

            $mail->Body    = $body;
            $mail->AltBody = "Bonjour $to_name,\n\nBienvenue sur Rappelez-moi.co !\n\nVotre code de vérification est : $verification_code\n\nCordialement,\nL'équipe Rappelez-moi.co";

            $mail->send();
            return true;
        } catch (Exception $e) {
            $errorMsg = $mail->ErrorInfo;
            error_log("Mailer Error (Activation): " . $errorMsg);
            return false;
        }
    }
}
?>
