<?php
// models/Lead.php
class Lead
{
    private $conn;
    private $table_name = "leads";

    public $id;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $zip_code;
    public $city;
    public $sector;
    public $need;
    public $budget;
    public $time_slot;
    public $user_id;
    public $status;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function read($provider_id = null)
    {
        $query = "SELECT l.*, 
                         la.provider_id as assigned_to,
                         cp.first_name AS client_first_name, 
                         cp.last_name AS client_last_name,
                         cp.phone AS client_profile_phone,
                         cp.city AS client_profile_city
                  FROM " . $this->table_name . " l
                  LEFT JOIN lead_assignments la ON l.id = la.lead_id
                  LEFT JOIN user_profiles cp ON l.user_id = cp.id";
        
        if ($provider_id) {
            $query .= " WHERE (la.provider_id IS NULL OR la.provider_id = ?) ";
        }
        
        $query .= " ORDER BY l.created_at DESC";
        $stmt = $this->conn->prepare($query);
        if ($provider_id) {
            $stmt->execute([$provider_id]);
        } else {
            $stmt->execute();
        }
        return $stmt;
    }

    public function readRecommended($sectorsJson, $provider_id = null) {
        $sectors = @json_decode($sectorsJson, true);
        if (empty($sectors) || !is_array($sectors)) return $this->read($provider_id);

        $placeholders = implode(',', array_fill(0, count($sectors), '?'));
        $query = "SELECT l.*, 
                         la.provider_id as assigned_to,
                         cp.first_name AS client_first_name, 
                         cp.last_name AS client_last_name,
                         cp.phone AS client_profile_phone,
                         cp.city AS client_profile_city
                  FROM " . $this->table_name . " l
                  LEFT JOIN lead_assignments la ON l.id = la.lead_id
                  LEFT JOIN user_profiles cp ON l.user_id = cp.id
                  WHERE l.sector IN ($placeholders) ";
        
        if ($provider_id) {
            $query .= " AND (la.provider_id IS NULL OR la.provider_id = ?) ";
        }
        
        $query .= " ORDER BY l.created_at DESC";
                   
        $stmt = $this->conn->prepare($query);
        $idx = 1;
        foreach ($sectors as $sector) {
            $stmt->bindValue($idx++, $sector);
        }
        if ($provider_id) {
            $stmt->bindValue($idx, $provider_id);
        }
        $stmt->execute();
        return $stmt;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    id = :id,
                    name = :name,
                    email = :email,
                    phone = :phone,
                    zip_code = :zip_code,
                    city = :city,
                    address = :address,
                    sector = :sector,
                    need = :need,
                    budget = :budget,
                    time_slot = :time_slot,
                    user_id = :user_id,
                    status = :status";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->need = htmlspecialchars(strip_tags($this->need));

        // Bind
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':zip_code', $this->zip_code);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':sector', $this->sector);
        $stmt->bindParam(':need', $this->need);
        $stmt->bindParam(':budget', $this->budget);
        $stmt->bindParam(':time_slot', $this->time_slot);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            return true;
        }
        error_log("Lead Create Failed: " . print_r($stmt->errorInfo(), true));
        return false;
    }
    // Lire les leads assignés à un prestataire
    public function readByProvider($provider_id)
    {
        $query = "SELECT l.*,
                         cp.first_name AS client_first_name, 
                         cp.last_name AS client_last_name,
                         cp.phone AS client_profile_phone,
                         cp.city AS client_profile_city
                FROM " . $this->table_name . " l
                INNER JOIN lead_assignments la ON l.id = la.lead_id
                LEFT JOIN user_profiles cp ON l.user_id = cp.id
                WHERE la.provider_id = ?
                ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $provider_id);
        $stmt->execute();
        return $stmt;
    }

    // Lire les leads récents assignés à un prestataire
    public function readRecentByProvider($provider_id, $limit = 5)
    {
        $query = "SELECT l.*,
                         cp.first_name AS client_first_name, 
                         cp.last_name AS client_last_name
                FROM " . $this->table_name . " l
                INNER JOIN lead_assignments la ON l.id = la.lead_id
                LEFT JOIN user_profiles cp ON l.user_id = cp.id
                WHERE la.provider_id = ?
                ORDER BY l.created_at DESC
                LIMIT 0, " . (int)$limit;

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $provider_id);
        $stmt->execute();
        return $stmt;
    }

    // Lire les leads appartenant à un client avec les infos de l'expert assigné
    public function readByClient($user_id)
    {
        $query = "SELECT l.*,
                         la.provider_id as assigned_to,
                         cp.company_name as provider_company,
                         CONCAT(cp.first_name, ' ', cp.last_name) as provider_name,
                         cp.phone as provider_phone,
                         cp.email as provider_email
                FROM " . $this->table_name . " l
                LEFT JOIN lead_assignments la ON l.id = la.lead_id
                LEFT JOIN user_profiles cp ON la.provider_id = cp.id
                WHERE l.user_id = ?
                ORDER BY l.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Lire les leads récents appartenant à un client
    public function readRecentByClient($user_id, $limit = 5)
    {
        $query = "SELECT * FROM " . $this->table_name . "
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT 0, " . (int)$limit;

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }

    // Créer une assignment (lier un lead à un prestataire)
    public function createAssignment($lead_id, $provider_id)
    {
        $query = "INSERT INTO lead_assignments (id, lead_id, provider_id) VALUES (:id, :lead_id, :provider_id)";
        $stmt = $this->conn->prepare($query);

        // Generate UUID for assignment
        $assignment_id = sprintf(
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

        $stmt->bindParam(':id', $assignment_id);
        $stmt->bindParam(':lead_id', $lead_id);
        $stmt->bindParam(':provider_id', $provider_id);
        $stmt->bindParam(':provider_id', $provider_id);

        if ($stmt->execute()) {
            return true;
        }
        error_log("Lead Assignment Create Failed: " . print_r($stmt->errorInfo(), true));
        return false;
    }
}
?>
