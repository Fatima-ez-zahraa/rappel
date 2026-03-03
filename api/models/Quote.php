<?php
// api/models/Quote.php

class Quote
{
    private $conn;
    private $table_name = "quotes";

    public $id;
    public $provider_id;
    public $client_name;
    public $project_name;
    public $amount;
    public $items_count;
    public $lead_id;
    public $status;
    public $doc_path;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function readByProvider($provider_id)
    {
        $query = "SELECT q.*, 
                         l.phone as contact_phone, 
                         l.email as contact_email, 
                         l.address as contact_address,
                         cp.first_name as client_first_name,
                         cp.last_name as client_last_name,
                         cp.phone as client_profile_phone,
                         cp.city as client_profile_city
                  FROM " . $this->table_name . " q
                  LEFT JOIN leads l ON q.lead_id = l.id
                  LEFT JOIN user_profiles cp ON l.user_id = cp.id
                  WHERE q.provider_id = ? 
                  ORDER BY q.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $provider_id);
        $stmt->execute();
        return $stmt;
    }

    public function readAll()
    {
        $query = "SELECT q.*, 
                         l.phone as contact_phone, 
                         l.email as contact_email, 
                         l.address as contact_address,
                         cp.first_name as client_first_name,
                         cp.last_name as client_last_name,
                         cp.phone as client_profile_phone,
                         cp.city as client_profile_city
                  FROM " . $this->table_name . " q
                  LEFT JOIN leads l ON q.lead_id = l.id
                  LEFT JOIN user_profiles cp ON l.user_id = cp.id
                  ORDER BY q.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readByLead($lead_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE lead_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $lead_id);
        $stmt->execute();
        return $stmt;
    }

    public function readByClient($user_id)
    {
        $query = "SELECT q.*, l.need, up.company_name as provider_company
                  FROM " . $this->table_name . " q
                  LEFT JOIN leads l ON q.lead_id = l.id
                  LEFT JOIN user_profiles up ON q.provider_id = up.id
                  LEFT JOIN user_profiles cu ON cu.id = :uid_profile
                  WHERE l.user_id = :uid_direct
                     OR (
                        l.user_id IS NULL
                        AND l.email IS NOT NULL
                        AND cu.email IS NOT NULL
                        AND LOWER(TRIM(l.email)) = LOWER(TRIM(cu.email))
                     )
                     OR (
                        l.user_id IS NULL
                        AND l.name IS NOT NULL
                        AND (
                            (cu.company_name IS NOT NULL AND LOWER(TRIM(l.name)) = LOWER(TRIM(cu.company_name)))
                            OR LOWER(TRIM(l.name)) = LOWER(TRIM(CONCAT(COALESCE(cu.first_name, ''), ' ', COALESCE(cu.last_name, ''))))
                            OR (cu.email IS NOT NULL AND LOWER(TRIM(l.name)) = LOWER(TRIM(cu.email)))
                        )
                     )
                     OR (
                        (cu.company_name IS NOT NULL AND LOWER(TRIM(q.client_name)) = LOWER(TRIM(cu.company_name)))
                        OR LOWER(TRIM(q.client_name)) = LOWER(TRIM(CONCAT(COALESCE(cu.first_name, ''), ' ', COALESCE(cu.last_name, ''))))
                        OR (cu.email IS NOT NULL AND LOWER(TRIM(q.client_name)) = LOWER(TRIM(cu.email)))
                     )
                  ORDER BY q.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uid_profile', $user_id);
        $stmt->bindParam(':uid_direct', $user_id);
        $stmt->execute();
        return $stmt;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    id = :id,
                    provider_id = :provider_id,
                    lead_id = :lead_id,
                    client_name = :client_name,
                    project_name = :project_name,
                    amount = :amount,
                    items_count = :items_count,
                    status = :status,
                    doc_path = :doc_path";

        $stmt = $this->conn->prepare($query);

        // Bind
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':provider_id', $this->provider_id);
        $stmt->bindParam(':lead_id', $this->lead_id);
        $stmt->bindParam(':client_name', $this->client_name);
        $stmt->bindParam(':project_name', $this->project_name);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':items_count', $this->items_count);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':doc_path', $this->doc_path);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . "
                SET
                    client_name = :client_name,
                    project_name = :project_name,
                    amount = :amount,
                    items_count = :items_count,
                    status = :status,
                    lead_id = :lead_id,
                    doc_path = :doc_path
                WHERE id = :id AND provider_id = :provider_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':provider_id', $this->provider_id);
        $stmt->bindParam(':client_name', $this->client_name);
        $stmt->bindParam(':project_name', $this->project_name);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':items_count', $this->items_count);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':lead_id', $this->lead_id);
        $stmt->bindParam(':doc_path', $this->doc_path);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND provider_id = :provider_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':provider_id', $this->provider_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readOne()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? AND provider_id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->bindParam(2, $this->provider_id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->lead_id = $row['lead_id'];
            $this->client_name = $row['client_name'];
            $this->project_name = $row['project_name'];
            $this->amount = $row['amount'];
            $this->items_count = $row['items_count'];
            $this->status = $row['status'];
            $this->doc_path = $row['doc_path'] ?? null;
            $this->created_at = $row['created_at'];
            return true;
        }
        return false;
    }
}
?>
