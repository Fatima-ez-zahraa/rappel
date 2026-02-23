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
            
            $need = htmlspecialchars($lead_details['need'] ?? 'Général');
            $time_slot = htmlspecialchars($lead_details['time_slot'] ?? 'Non spécifié');
            $phone = htmlspecialchars($lead_details['phone'] ?? '');

            $body = "
            <div style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; padding: 40px 20px;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 24px; overflow: hidden; shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);'>
                    <!-- Header -->
                    <div style='background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%); padding: 40px; text-align: center;'>
                        <h2 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.025em;'>Rappelez-moi.co</h2>
                        <p style='color: rgba(255,255,255,0.9); margin-top: 8px; font-size: 16px;'>Confirmation de votre demande</p>
                    </div>

                    <!-- Content -->
                    <div style='padding: 40px;'>
                        <h1 style='color: #0f172a; font-size: 28px; font-weight: 700; margin: 0 0 16px 0;'>Bonjour $to_name,</h1>
                        <p style='color: #475569; font-size: 16px; line-height: 1.6; margin: 0 0 32px 0;'>
                            Nous avons bien reçu votre demande de rappel. Un de nos experts partenaires vous contactera prochainement selon vos préférences.
                        </p>

                        <!-- Details Box -->
                        <div style='background-color: #f1f5f9; border-radius: 16px; padding: 24px; margin-bottom: 32px;'>
                            <h3 style='color: #0f172a; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; margin: 0 0 16px 0;'>Détails de la demande</h3>
                            
                            <table style='width: 100%; border-collapse: collapse;'>
                                <tr>
                                    <td style='padding: 8px 0; color: #64748b; font-size: 14px;'>Besoin</td>
                                    <td style='padding: 8px 0; color: #0f172a; font-size: 14px; font-weight: 600; text-align: right;'>$need</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; color: #64748b; font-size: 14px;'>Créneau souhaité</td>
                                    <td style='padding: 8px 0; color: #0f172a; font-size: 14px; font-weight: 600; text-align: right;'>$time_slot</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; color: #64748b; font-size: 14px;'>Téléphone</td>
                                    <td style='padding: 8px 0; color: #0f172a; font-size: 14px; font-weight: 600; text-align: right;'>$phone</td>
                                </tr>
                            </table>
                        </div>

                        <div style='text-align: center;'>
                            <p style='color: #94a3b8; font-size: 13px; font-style: italic; margin-bottom: 0;'>
                                Préparez vos questions, notre expert sera là pour vous accompagner.
                            </p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div style='padding: 32px 40px; border-top: 1px solid #f1f5f9; text-align: center;'>
                        <p style='color: #475569; font-size: 14px; font-weight: 600; margin: 0;'>Cordialement,</p>
                        <p style='color: #10b981; font-size: 14px; font-weight: 700; margin: 4px 0 0 0;'>L'équipe Rappelez-moi.co</p>
                        
                        <div style='margin-top: 24px;'>
                            <p style='color: #94a3b8; font-size: 11px; margin: 0;'>
                                Cet e-mail est une notification automatique, merci de ne pas y répondre directement.
                            </p>
                        </div>
                    </div>
                </div>
            </div>";


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
