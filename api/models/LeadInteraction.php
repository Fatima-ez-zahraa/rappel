<?php
// api/models/LeadInteraction.php

class LeadInteraction
{
    private $conn;
    private $table_name = "lead_interactions";

    public $id;
    public $lead_id;
    public $provider_id;
    public $comment;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function readByLead($lead_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE lead_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $lead_id);
        $stmt->execute();
        return $stmt;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    id = :id,
                    lead_id = :lead_id,
                    provider_id = :provider_id,
                    comment = :comment";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->comment = htmlspecialchars(strip_tags($this->comment));

        // Bind
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':lead_id', $this->lead_id);
        $stmt->bindParam(':provider_id', $this->provider_id);
        $stmt->bindParam(':comment', $this->comment);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
