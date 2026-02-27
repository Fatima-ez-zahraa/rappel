<?php
// controllers/AuthController.php

require_once 'config/db.php';
require_once 'models/User.php';
require_once 'utils/JwtUtils.php';
require_once 'utils/Mailer.php';

class AuthController
{
    private $db;
    private $user;
    private $jwt;
    private $mailer;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
        $this->jwt = new JwtUtils();
        $this->mailer = new Mailer();
    }

    public function signup()
    {
        // Récupérer les données brutes POST
        $data = json_decode(file_get_contents("php://input"));

        if (
            !empty($data->email) &&
            !empty($data->password)
        ) {
            // Vérifier existance
            $this->user->email = $data->email;
            if ($this->user->emailExists()) {
                http_response_code(400);
                echo json_encode(["error" => "Email déjà utilisé."]);
                return;
            }

            // Générer UUID (méthode simple v4)
            $this->user->id = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff)
            );

            $this->user->password = password_hash($data->password, PASSWORD_BCRYPT);
            $this->user->first_name = $data->first_name ?? $data->firstName ?? ''; 
            $this->user->last_name = $data->last_name ?? $data->lastName ?? '';
            $this->user->siret = $data->siret ?? null;
            $this->user->company_name = $data->company_name ?? $data->companyName ?? null;
            $this->user->role = $data->role ?? 'provider';

            // Autres champs
            $this->user->creation_year = $data->creation_year ?? $data->creationYear ?? null;
            $this->user->address = $data->address ?? null;
            $this->user->zip = $data->zip ?? null;
            $this->user->city = $data->city ?? null;
            $this->user->phone = $data->phone ?? null;
            $this->user->legal_form = $data->legal_form ?? $data->legalForm ?? null;
            $this->user->description = $data->description ?? null;
            $this->user->zone = $data->zone ?? null;

            // JSON Encode sectors
            $sectors = $data->sectors ?? $data->sector ?? [];
            if (is_string($sectors)) {
                $this->user->sectors = json_encode([$sectors]);
            } else {
                $this->user->sectors = json_encode($sectors);
            }

            // Générer le code de vérification (6 chiffres)
            $verification_code = sprintf("%06d", mt_rand(0, 999999));
            $this->user->verification_code = $verification_code;
            $this->user->is_verified = 0;

            if ($this->user->create()) {
                // Link any existing anonymous leads to this new user by email
                $linkQuery = "UPDATE leads SET user_id = :user_id WHERE email = :email AND user_id IS NULL";
                $linkStmt = $this->db->prepare($linkQuery);
                $linkStmt->bindParam(':user_id', $this->user->id);
                $linkStmt->bindParam(':email', $this->user->email);
                $linkStmt->execute();

                http_response_code(201);

                $token_payload = [
                    "id" => $this->user->id,
                    "email" => $this->user->email,
                    "role" => $this->user->role,
                    "exp" => time() + (60 * 60 * 24) // 24h
                ];
                $token = $this->jwt->generate($token_payload);

                // Envoyer l'email d'activation
                $email_sent = $this->mailer->sendActivationEmail(
                    $this->user->email,
                    $this->user->first_name . ' ' . $this->user->last_name,
                    $verification_code
                );

                if ($email_sent) {
                    echo json_encode([
                        "message" => "Utilisateur créé. Un email d'activation a été envoyé.",
                        "user" => [
                            "id" => $this->user->id,
                            "email" => $this->user->email,
                            "first_name" => $this->user->first_name,
                            "last_name" => $this->user->last_name,
                            "role" => $this->user->role,
                            "company_name" => $this->user->company_name,
                            "siret" => $this->user->siret,
                            "phone" => $this->user->phone,
                            "address" => $this->user->address,
                            "city" => $this->user->city,
                            "zip" => $this->user->zip,
                            "creation_year" => $this->user->creation_year,
                            "legal_form" => $this->user->legal_form,
                            "description" => $this->user->description,
                            "zone" => $this->user->zone,
                            "sectors" => $this->user->sectors
                        ],
                        "session" => [
                            "access_token" => $token
                        ]
                    ]);
                } else {
                    http_response_code(201); // User created but email failed
                    echo json_encode([
                        "message" => "Utilisateur créé, mais l'envoi de l'email d'activation a échoué. Veuillez contacter le support ou demander un renvoi de code.",
                        "emailError" => true,
                        "user" => [
                            "id" => $this->user->id,
                            "email" => $this->user->email
                        ],
                        "session" => [
                            "access_token" => $token
                        ]
                    ]);
                }
            } else {
                http_response_code(503);
                echo json_encode(["error" => "Impossible de créer l'utilisateur."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Données incomplètes."]);
        }
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"));

        $this->user->email = $data->email ?? '';

        if ($this->user->emailExists()) {
            // Vérifier si le compte est activé
            if (!$this->user->is_verified) {
                http_response_code(403);
                echo json_encode([
                    "error" => "Compte non vérifié. Veuillez vérifier votre email.",
                    "requiresVerification" => true
                ]);
                return;
            }

            if (password_verify($data->password, $this->user->password)) {
                $token_payload = [
                    "id" => $this->user->id,
                    "email" => $this->user->email,
                    "role" => $this->user->role,
                    "exp" => time() + (60 * 60 * 24)
                ];
                $token = $this->jwt->generate($token_payload);

                http_response_code(200);
                echo json_encode([
                        "user" => [
                            "id" => $this->user->id,
                            "email" => $this->user->email,
                            "first_name" => $this->user->first_name,
                            "last_name" => $this->user->last_name,
                            "role" => $this->user->role,
                            "is_verified" => (bool)$this->user->is_verified,
                            "company_name" => $this->user->company_name,
                            "siret" => $this->user->siret,
                            "phone" => $this->user->phone,
                            "address" => $this->user->address,
                            "city" => $this->user->city,
                            "zip" => $this->user->zip,
                            "creation_year" => $this->user->creation_year,
                            "legal_form" => $this->user->legal_form,
                            "description" => $this->user->description,
                            "zone" => $this->user->zone,
                            "sectors" => $this->user->sectors
                        ],
                    "session" => [
                        "access_token" => $token
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(["error" => "Mot de passe incorrect."]);
            }
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Email introuvable."]);
        }
    }

    public function changePassword()
    {
        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? $_SERVER['HTTP_AUTHORISATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORISATION'] ?? '';

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(["error" => "Non autorise"]);
            return;
        }

        $token = $matches[1];
        $payload = $this->jwt->validate($token);
        if (!$payload) {
            http_response_code(401);
            echo json_encode(["error" => "Token invalide"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->current_password) || empty($data->new_password)) {
            http_response_code(400);
            echo json_encode(["error" => "Mot de passe actuel et nouveau mot de passe requis."]);
            return;
        }

        if (strlen($data->new_password) < 8) {
            http_response_code(400);
            echo json_encode(["error" => "Le nouveau mot de passe doit contenir au moins 8 caracteres."]);
            return;
        }

        $payload = (array)$payload;
        $this->user->id = $payload['id'];
        [$ok, $message] = $this->user->changePassword((string)$data->current_password, (string)$data->new_password);

        if (!$ok) {
            http_response_code(400);
            echo json_encode(["error" => $message]);
            return;
        }

        echo json_encode(["message" => $message]);
    }

    public function verify()
    {
        $this->verifyEmail();
    }

    public function verifyEmail()
    {
        try {
            $data = json_decode(file_get_contents("php://input"));

            if (empty($data->email) || empty($data->code)) {
                http_response_code(400);
                echo json_encode(["error" => "Email et code requis."]);
                return;
            }

            $this->user->email = $data->email;

            if ($this->user->verifyEmail($data->code)) {
                // Code valide, compte activé
                // Récupérer les données de l'utilisateur
                if ($this->user->emailExists()) {
                    $token_payload = [
                        "id" => $this->user->id,
                        "email" => $this->user->email,
                        "role" => $this->user->role,
                        "exp" => time() + (60 * 60 * 24)
                    ];
                    $token = $this->jwt->generate($token_payload);

                    http_response_code(200);
                    echo json_encode([
                        "message" => "Votre compte a été activé avec succès.",
                        "user" => [
                            "id" => $this->user->id,
                            "email" => $this->user->email,
                            "first_name" => $this->user->first_name,
                            "last_name" => $this->user->last_name,
                            "role" => $this->user->role,
                            "is_verified" => true,
                            "company_name" => $this->user->company_name,
                            "siret" => $this->user->siret,
                            "phone" => $this->user->phone,
                            "address" => $this->user->address,
                            "city" => $this->user->city,
                            "zip" => $this->user->zip,
                            "creation_year" => $this->user->creation_year,
                            "legal_form" => $this->user->legal_form,
                            "description" => $this->user->description,
                            "zone" => $this->user->zone,
                            "sectors" => $this->user->sectors
                        ],
                        "session" => [
                            "access_token" => $token
                        ]
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Email introuvable après activation."]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Code de vérification invalide."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erreur interne: " . $e->getMessage()]);
        }
    }

    public function resendActivationEmail()
    {
        try {
            $data = json_decode(file_get_contents("php://input"));

            if (empty($data->email)) {
                http_response_code(400);
                echo json_encode(["error" => "Email requis."]);
                return;
            }

            $this->user->email = $data->email;

            if ($this->user->emailExists()) {
                // Vérifier si déjà vérifié
                if ($this->user->is_verified) {
                    http_response_code(400);
                    echo json_encode(["error" => "Compte déjà activé."]);
                    return;
                }

                // Générer un nouveau code
                $verification_code = sprintf("%06d", mt_rand(0, 999999));
                
                if ($this->user->updateVerificationCode($verification_code)) {
                    // Envoyer l'email
                    $email_sent = $this->mailer->sendActivationEmail(
                        $this->user->email,
                        $this->user->first_name . ' ' . $this->user->last_name,
                        $verification_code
                    );

                    if ($email_sent) {
                        http_response_code(200);
                        echo json_encode(["message" => "Email de vérification renvoyé."]);
                    } else {
                        http_response_code(500);
                        echo json_encode(["error" => "Erreur lors de l'envoi de l'email."]);
                    }
                } else {
                    http_response_code(503);
                    echo json_encode(["error" => "Erreur lors de la mise à jour du code."]);
                }
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Email introuvable."]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erreur interne: " . $e->getMessage()]);
        }
    }

    public function getProfile()
    {
        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? $_SERVER['HTTP_AUTHORISATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORISATION'] ?? '';

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $payload = $this->jwt->validate($token);
            if ($payload) {
                $payload = (array)$payload;
                $this->user->id = $payload['id'];
                if ($this->user->readOne()) {
                    http_response_code(200);
                    echo json_encode([
                        "user" => [
                            "id" => $this->user->id,
                            "email" => $this->user->email,
                            "first_name" => $this->user->first_name,
                            "last_name" => $this->user->last_name,
                            "role" => $this->user->role,
                            "company_name" => $this->user->company_name,
                            "siret" => $this->user->siret,
                            "is_verified" => (bool)$this->user->is_verified,
                            "phone" => $this->user->phone,
                            "address" => $this->user->address,
                            "city" => $this->user->city,
                            "zip" => $this->user->zip,
                            "creation_year" => $this->user->creation_year,
                            "legal_form" => $this->user->legal_form,
                            "sectors" => $this->user->sectors,
                            "description" => $this->user->description,
                            "zone" => $this->user->zone,
                            "subscription" => [
                                "plan_id" => $this->user->plan_id,
                                "plan_name" => $this->user->plan_name,
                                "plan_price" => $this->user->plan_price,
                                "lead_credits" => $this->user->lead_credits,
                                "max_leads" => $this->user->max_leads,
                                "status" => $this->user->subscription_status
                            ]
                        ]
                    ]);
                    return;
                }
            }
        }

        http_response_code(401);
        echo json_encode(["error" => "Non autorisé"]);
    }

    public function updateProfile()
    {
        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? $_SERVER['HTTP_AUTHORISATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORISATION'] ?? '';

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $payload = $this->jwt->validate($token);
            if ($payload) {
                $payload = (array)$payload;
                $this->user->id = $payload['id'];
                $data = json_decode(file_get_contents("php://input"), true);
                
                // Normalize sectors if present
                if (isset($data['sectors'])) {
                    if (is_string($data['sectors'])) {
                        // Handle comma-separated list
                        $sectors = array_map('trim', explode(',', $data['sectors']));
                        $data['sectors'] = json_encode(array_filter($sectors));
                    } else if (is_array($data['sectors'])) {
                        $data['sectors'] = json_encode($data['sectors']);
                    }
                }

                if ($this->user->update($data)) {
                    if ($this->user->readOne()) {
                        http_response_code(200);
                        echo json_encode([
                            "message" => "Profil mis à jour.",
                            "user" => [
                                "id" => $this->user->id,
                                "email" => $this->user->email,
                                "first_name" => $this->user->first_name,
                                "last_name" => $this->user->last_name,
                                "role" => $this->user->role,
                                "company_name" => $this->user->company_name,
                                "siret" => $this->user->siret,
                                "is_verified" => (bool)$this->user->is_verified,
                                "phone" => $this->user->phone,
                                "address" => $this->user->address,
                                "city" => $this->user->city,
                                "zip" => $this->user->zip,
                                "creation_year" => $this->user->creation_year,
                                "legal_form" => $this->user->legal_form,
                                "sectors" => $this->user->sectors,
                                "description" => $this->user->description,
                                "zone" => $this->user->zone
                            ]
                        ]);
                    } else {
                        http_response_code(200);
                        echo json_encode(["message" => "Profil mis à jour."]);
                    }
                } else {
                    http_response_code(503);
                    echo json_encode(["error" => "Erreur lors de la mise à jour."]);
                }
                return;
            }
        }

        http_response_code(401);
        echo json_encode(["error" => "Non autorisé"]);
    }

    public function forgotPassword()
    {
        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->email)) {
            http_response_code(400);
            echo json_encode(["error" => "Email requis."]);
            return;
        }

        $this->user->email = $data->email;
        if ($this->user->emailExists()) {
            // Generate token
            $token = bin2hex(random_bytes(32));
            // Expire in 1 hour
            $expires = date("Y-m-d H:i:s", time() + 3600);

            if ($this->user->updateResetToken($data->email, $token, $expires)) {
                // Determine base URL (simplified for now, should be from config)
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                $host = $_SERVER['HTTP_HOST'];
                $reset_link = "$protocol://$host/rappel/public/reset-password.php?token=$token";

                if ($this->mailer->sendResetPasswordEmail($this->user->email, $this->user->first_name, $reset_link)) {
                    echo json_encode(["message" => "Un email de réinitialisation a été envoyé."]);
                } else {
                    http_response_code(500);
                    echo json_encode(["error" => "Erreur lors de l'envoi de l'email."]);
                }
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Erreur lors de la génération du jeton."]);
            }
        } else {
            // Security: don't reveal if email exists or not
            echo json_encode(["message" => "Si cet email existe, un lien de réinitialisation a été envoyé."]);
        }
    }

    public function resetPassword()
    {
        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->token) || empty($data->password)) {
            http_response_code(400);
            echo json_encode(["error" => "Token et nouveau mot de passe requis."]);
            return;
        }

        if ($this->user->findByResetToken($data->token)) {
            $hashedPassword = password_hash($data->password, PASSWORD_BCRYPT);
            if ($this->user->resetPassword($this->user->id, $hashedPassword)) {
                echo json_encode(["message" => "Votre mot de passe a été réinitialisé avec succès."]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Erreur lors de la réinitialisation."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Lien invalide ou expiré."]);
        }
    }
}
?>
