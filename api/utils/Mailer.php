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
            $time_slot = htmlspecialchars($lead_details['time_slot'] ?? '-');
            $phone = htmlspecialchars($lead_details['phone'] ?? '');

            // Embed Logo
            $logoPath = __DIR__ . '/../../public/assets/img/logo.png';
            if (file_exists($logoPath)) {
                $mail->addEmbeddedImage($logoPath, 'logo_img');
                $headerContent = "<img src='cid:logo_img' alt='Rappelez-moi.co' height='40' style='display: block; margin: 0 auto;'>";
            } else {
                $headerContent = "<h2 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.025em;'>Rappelez-moi.co</h2>";
            }

            $body = "
            <div style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; padding: 40px 20px;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);'>
                    <!-- Header -->
                    <div style='background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%); padding: 40px; text-align: center;'>
                        $headerContent
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
            
            // Embed Logo
            $logoPath = __DIR__ . '/../../public/assets/img/logo.png';
            $logoHtml = "";
            if (file_exists($logoPath)) {
                $mail->addEmbeddedImage($logoPath, 'logo_img');
                $logoHtml = "<div style='text-align: center; margin-bottom: 24px;'><img src='cid:logo_img' alt='Rappelez-moi.co' height='40'></div>";
            }

            $body = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>";
            $body .= $logoHtml;
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

    public function sendInvoiceEmail($to_email, $to_name, $invoiceData)
    {
        $mail = new PHPMailer(true);

        try {
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

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom($this->username, 'Rappelez-moi.co');
            $mail->addAddress($to_email, $to_name);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Votre facture Rappelez-moi.co - ' . $invoiceData['invoice_number'];
            
            // Embed Logo
            $logoPath = __DIR__ . '/../../public/assets/img/logo.png';
            if (file_exists($logoPath)) {
                $mail->addEmbeddedImage($logoPath, 'logo_img');
                $headerContent = "<img src='cid:logo_img' alt='Rappelez-moi.co' height='40' style='display: block; margin: 0 auto;'>";
            } else {
                $headerContent = "<h2 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 800;'>Rappelez-moi.co</h2>";
            }

            $amount = number_format($invoiceData['amount'], 2, ',', ' ') . ' ' . ($invoiceData['currency'] ?? 'EUR');
            $date = date('d/m/Y', strtotime($invoiceData['created_at']));

            $body = "
            <div style='font-family: Arial, sans-serif; background-color: #f8fafc; padding: 40px 20px;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);'>
                    <div style='background: #234bae; padding: 30px; text-align: center;'>
                        $headerContent
                    </div>
                    <div style='padding: 40px;'>
                        <h1 style='color: #0f172a; font-size: 24px; font-weight: 700; margin: 0 0 20px 0;'>Bonjour $to_name,</h1>
                        <p style='color: #475569; font-size: 16px; line-height: 1.6; margin: 0 0 30px 0;'>
                            Veuillez trouver ci-joint les détails de votre facture relative à votre abonnement Rappelez-moi.co.
                        </p>
                        
                        <div style='background-color: #f1f5f9; border-radius: 12px; padding: 20px; margin-bottom: 30px;'>
                            <table style='width: 100%; border-collapse: collapse;'>
                                <tr>
                                    <td style='padding: 8px 0; color: #64748b;'>N° Facture</td>
                                    <td style='padding: 8px 0; color: #0f172a; font-weight: 700; text-align: right;'>{$invoiceData['invoice_number']}</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; color: #64748b;'>Date</td>
                                    <td style='padding: 8px 0; color: #0f172a; font-weight: 700; text-align: right;'>$date</td>
                                </tr>
                                <tr>
                                    <td style='padding: 8px 0; color: #64748b;'>Montant</td>
                                    <td style='padding: 8px 0; color: #234bae; font-weight: 800; font-size: 18px; text-align: right;'>$amount</td>
                                </tr>
                            </table>
                        </div>

                        <div style='text-align: center;'>
                            <p style='color: #94a3b8; font-size: 14px;'>
                                Vous pouvez retrouver l'historique complet de vos factures dans votre espace personnel.
                            </p>
                        </div>
                    </div>
                    <div style='padding: 20px; border-top: 1px solid #f1f5f9; text-align: center;'>
                        <p style='color: #64748b; font-size: 12px; margin: 0;'>
                            © " . date('Y') . " Rappelez-moi.co. Tous droits réservés.
                        </p>
                    </div>
                </div>
            </div>";

            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error (Invoice): " . $mail->ErrorInfo);
            return false;
        }
    }

    public function sendResetPasswordEmail($to_email, $to_name, $reset_link)
    {
        $mail = new PHPMailer(true);

        try {
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

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom($this->username, 'Rappelez-moi.co');
            $mail->addAddress($to_email, $to_name);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Réinitialisation de votre mot de passe - Rappelez-moi.co';
            
            $logoPath = __DIR__ . '/../../public/assets/img/logo.png';
            $logoHtml = "";
            if (file_exists($logoPath)) {
                $mail->addEmbeddedImage($logoPath, 'logo_img');
                $logoHtml = "<div style='text-align: center; margin-bottom: 24px;'><img src='cid:logo_img' alt='Rappelez-moi.co' height='40'></div>";
            }

            $body = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 12px;'>";
            $body .= $logoHtml;
            $body .= "<h1 style='color: #333; font-size: 20px;'>Bonjour $to_name,</h1>";
            $body .= "<p style='font-size: 16px; color: #555;'>Vous avez demandé la réinitialisation de votre mot de passe pour votre compte <strong>Rappelez-moi.co</strong>.</p>";
            $body .= "<p style='font-size: 16px; color: #555;'>Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe :</p>";
            $body .= "<div style='text-align: center; margin: 32px 0;'>";
            $body .= "<a href='$reset_link' style='background-color: #10b981; color: white; padding: 16px 32px; text-decoration: none; font-weight: bold; rounded: 8px; border-radius: 8px; display: inline-block;'>Réinitialiser mon mot de passe</a>";
            $body .= "</div>";
            $body .= "<p style='font-size: 14px; color: #777;'>Ce lien est valable pendant 1 heure.</p>";
            $body .= "<p style='font-size: 14px; color: #777;'>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email en toute sécurité.</p>";
            $body .= "<hr style='border: none; border-top: 1px solid #eee; margin: 30px 0;'>";
            $body .= "<p style='font-size: 14px; color: #999;'>Cordialement,<br>L'équipe Rappelez-moi.co</p>";
            $body .= "</div>";

            $mail->Body    = $body;
            $mail->AltBody = "Bonjour $to_name,\n\nVous avez demandé la réinitialisation de votre mot de passe.\n\nCliquez sur ce lien pour continuer : $reset_link\n\nCe lien est valable 1 heure.\n\nCordialement,\nL'équipe Rappelez-moi.co";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error (Reset): " . $mail->ErrorInfo);
            return false;
        }
    }
    public function sendLeadNotificationToProvider($to_email, $to_name, $lead_details)
    {
        $mail = new PHPMailer(true);

        try {
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

            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->Timeout = 5;
            $mail->Timelimit = 5;

            $mail->setFrom($this->username, 'Rappelez-moi.co');
            $mail->addAddress($to_email, $to_name);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Nouveau Lead disponible : ' . ($lead_details['sector'] ?? 'Secteur non spécifié');
            
            // Embed Logo
            $logoPath = __DIR__ . '/../../public/assets/img/logo.png';
            if (file_exists($logoPath)) {
                $mail->addEmbeddedImage($logoPath, 'logo_img');
                $headerContent = "<img src='cid:logo_img' alt='Rappelez-moi.co' height='40' style='display: block; margin: 0 auto;'>";
            } else {
                $headerContent = "<h2 style='color: #ffffff; margin: 0; font-size: 24px; font-weight: 800;'>Rappelez-moi.co</h2>";
            }

            $sector = htmlspecialchars($lead_details['sector'] ?? '-');
            $city = htmlspecialchars($lead_details['city'] ?? '-');
            $budget = htmlspecialchars($lead_details['budget'] ?? 'Non spécifié');

            $body = "
            <div style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; background-color: #f0f4f8; padding: 40px 20px;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 10px 25px rgba(14, 22, 72, 0.1);'>
                    <div style='background: #0E1648; padding: 40px; text-align: center;'>
                        $headerContent
                        <p style='color: #94a3b8; margin-top: 12px; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;'>Opportunité d'Affaires</p>
                    </div>

                    <div style='padding: 40px;'>
                        <h1 style='color: #0f172a; font-size: 24px; font-weight: 800; margin: 0 0 16px 0;'>Bonjour $to_name,</h1>
                        <p style='color: #475569; font-size: 16px; line-height: 1.6; margin: 0 0 32px 0;'>
                            Une nouvelle demande correspondant à votre expertise vient d'être publiée. Soyez le premier à y répondre !
                        </p>

                        <div style='border: 2px solid #f1f5f9; border-radius: 20px; padding: 24px; margin-bottom: 32px;'>
                            <table style='width: 100%; border-collapse: collapse;'>
                                <tr>
                                    <td style='padding: 10px 0; color: #64748b; font-size: 14px;'>Secteur</td>
                                    <td style='padding: 10px 0; color: #0E1648; font-size: 15px; font-weight: 800; text-align: right;'>$sector</td>
                                </tr>
                                <tr>
                                    <td style='padding: 10px 0; color: #64748b; font-size: 14px;'>Localisation</td>
                                    <td style='padding: 10px 0; color: #0E1648; font-size: 15px; font-weight: 700; text-align: right;'>$city</td>
                                </tr>
                                <tr>
                                    <td style='padding: 10px 0; color: #64748b; font-size: 14px;'>Budget est.</td>
                                    <td style='padding: 10px 0; color: #10b981; font-size: 15px; font-weight: 800; text-align: right;'>$budget €</td>
                                </tr>
                            </table>
                        </div>

                        <div style='text-align: center;'>
                            <a href='http://localhost/rappel/public/pro/leads.php' style='display: inline-block; background-color: #0E1648; color: #ffffff; padding: 18px 36px; text-decoration: none; font-weight: 900; border-radius: 14px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em; box-shadow: 0 10px 15px -3px rgba(14, 22, 72, 0.3);'>Voir le Lead maintenant</a>
                        </div>
                    </div>

                    <div style='padding: 32px 40px; background-color: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center;'>
                        <p style='color: #64748b; font-size: 12px; margin: 0;'>
                            Ceci est une notification automatique de Rappelez-moi.co. Pour ne plus recevoir ces alertes, modifiez vos paramètres de notification dans votre espace expert.
                        </p>
                    </div>
                </div>
            </div>";

            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Provider Lead Notification Error: " . $mail->ErrorInfo);
            return false;
        }
    }
}
?>
