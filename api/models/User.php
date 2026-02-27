<?php
// models/User.php

class User
{
    private $conn;
    private $table_name = "user_profiles";

    public $id;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $siret;
    public $company_name;
    public $role;
    public $creation_year;
    public $address;
    public $zip;
    public $city;
    public $phone;
    public $sectors;
    public $legal_form;
    public $verification_code;
    public $is_verified;
    public $description;
    public $zone;
    public $plan_id;
    public $lead_credits;
    public $plan_name;
    public $plan_price;
    public $max_leads;
    public $subscription_status;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Créer un utilisateur
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    id = :id,
                    email = :email,
                    password = :password,
                    first_name = :first_name,
                    last_name = :last_name,
                    siret = :siret,
                    company_name = :company_name,
                    role = :role,
                    creation_year = :creation_year,
                    address = :address,
                    zip = :zip,
                    city = :city,
                    phone = :phone,
                    sectors = :sectors,
                    legal_form = :legal_form,
                    verification_code = :verification_code,
                    is_verified = :is_verified,
                    description = :description,
                    zone = :zone";

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':siret', $this->siret);
        $stmt->bindParam(':company_name', $this->company_name);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':creation_year', $this->creation_year);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':zip', $this->zip);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':sectors', $this->sectors);
        $stmt->bindParam(':legal_form', $this->legal_form);
        $stmt->bindParam(':verification_code', $this->verification_code);
        $stmt->bindParam(':is_verified', $this->is_verified);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':zone', $this->zone);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Vérifier si email existe
    public function emailExists()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->password = $row['password'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->role = $row['role'];
            $this->is_verified = $row['is_verified'];
            
            // Populate all other fields
            $this->siret = $row['siret'] ?? null;
            $this->company_name = $row['company_name'] ?? null;
            $this->creation_year = $row['creation_year'] ?? null;
            $this->address = $row['address'] ?? null;
            $this->zip = $row['zip'] ?? null;
            $this->city = $row['city'] ?? null;
            $this->phone = $row['phone'] ?? null;
            $this->legal_form = $row['legal_form'] ?? null;
            $this->description = $row['description'] ?? null;
            $this->zone = $row['zone'] ?? null;
            $this->sectors = $row['sectors'] ?? '[]';
            $this->plan_id = $row['plan_id'] ?? null;
            $this->lead_credits = (int)($row['lead_credits'] ?? 0);
            $this->subscription_status = $row['subscription_status'] ?? 'inactive';
            
            return true;
        }
        return false;
    }

    // Vérifier le code d'activation et activer le compte
    public function verifyEmail($code)
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ? AND verification_code = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->bindParam(2, $code);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $update_query = "UPDATE " . $this->table_name . " SET is_verified = 1, verification_code = NULL WHERE email = ?";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->bindParam(1, $this->email);
            return $update_stmt->execute();
        }
        return false;
    }

    public function updateVerificationCode($code)
    {
        $query = "UPDATE " . $this->table_name . " SET verification_code = ? WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $code);
        $stmt->bindParam(2, $this->email);
        return $stmt->execute();
    }

    // Lire les infos d'un utilisateur par ID
    public function readOne()
    {
        $query = "SELECT u.id, u.email, u.first_name, u.last_name, u.siret, u.company_name, u.role, 
                         u.creation_year, u.address, u.zip, u.city, u.phone, u.legal_form, 
                         u.is_verified, u.sectors, u.description, u.zone, 
                         u.plan_id, u.lead_credits, u.subscription_status,
                         p.name as plan_name, p.price as plan_price, p.max_leads
                  FROM " . $this->table_name . " u
                  LEFT JOIN subscription_plans p ON u.plan_id = p.id
                  WHERE u.id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->email = $row['email'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->siret = $row['siret'];
            $this->company_name = $row['company_name'];
            $this->role = $row['role'];
            $this->creation_year = $row['creation_year'];
            $this->address = $row['address'];
            $this->zip = $row['zip'];
            $this->city = $row['city'];
            $this->phone = $row['phone'];
            $this->legal_form = $row['legal_form'];
            $this->sectors = $row['sectors'];
            $this->description = $row['description'];
            $this->zone = $row['zone'];
            $this->is_verified = $row['is_verified'];
            $this->plan_id = $row['plan_id'];
            $this->lead_credits = (int)($row['lead_credits'] ?? 0);
            $this->subscription_status = $row['subscription_status'];
            $this->plan_name = $row['plan_name'];
            $this->plan_price = $row['plan_price'];
            $this->max_leads = (int)($row['max_leads'] ?? 0);

            return true;
        }

        return false;
    }

    // Mettre à jour les infos d'un utilisateur
    public function update($data)
    {
        if (empty($data)) return true;

        $fields = [];
        $values = [];
        $allowedFields = [
            'first_name', 'last_name', 'email', 'company_name', 'siret', 'legal_form', 
            'creation_year', 'address', 'zip', 'city', 'phone', 'sectors', 'description', 'zone'
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "$key = :$key";
                $values[":$key"] = $value;
            }
        }

        if (empty($fields)) return true;

        $query = "UPDATE " . $this->table_name . " SET " . implode(", ", $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $values[':id'] = $this->id;

        return $stmt->execute($values);
    }

    public function changePassword(string $currentPassword, string $newPassword): array
    {
        $query = "SELECT password FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return [false, "Utilisateur introuvable."];
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $hash = $row['password'] ?? '';
        if (!$hash || !password_verify($currentPassword, $hash)) {
            return [false, "Mot de passe actuel incorrect."];
        }

        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $updateQuery = "UPDATE " . $this->table_name . " SET password = ? WHERE id = ?";
        $updateStmt = $this->conn->prepare($updateQuery);
        $ok = $updateStmt->execute([$newHash, $this->id]);

        if (!$ok) {
            return [false, "Echec de la mise a jour du mot de passe."];
        }

        return [true, "Mot de passe mis a jour."];
    }

    public function updateResetToken($email, $token, $expires)
    {
        $query = "UPDATE " . $this->table_name . " SET reset_token = ?, reset_expires = ? WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$token, $expires, $email]);
    }

    public function findByResetToken($token)
    {
        $query = "SELECT id, email, first_name, last_name, role FROM " . $this->table_name . " 
                  WHERE reset_token = ? AND reset_expires > NOW() LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$token]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->email = $row['email'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->role = $row['role'];
            return true;
        }
        return false;
    }

    public function resetPassword($id, $newHashedPassword)
    {
        $query = "UPDATE " . $this->table_name . " SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$newHashedPassword, $id]);
    }

    /**
     * Billing: Check if user has credits
     */
    public function hasEnoughCredits() {
        $query = "SELECT lead_credits FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['lead_credits'] ?? 0) > 0;
    }

    /**
     * Billing: Deduct one credit
     */
    public function deductCredit() {
        $query = "UPDATE " . $this->table_name . " SET lead_credits = lead_credits - 1 WHERE id = ? AND lead_credits > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    /**
     * Find providers by sector
     */
    public function findBySector($sector) {
        $query = "SELECT id, email, first_name, last_name, company_name 
                  FROM " . $this->table_name . " 
                  WHERE role = 'provider' 
                  AND (sectors LIKE :sector_match OR sectors = :sector_exact)";
        
        $stmt = $this->conn->prepare($query);
        $sector_match = '%"' . $sector . '"%';
        $stmt->bindParam(':sector_match', $sector_match);
        $stmt->bindParam(':sector_exact', $sector);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
