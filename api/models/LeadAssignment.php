<?php
// models/LeadAssignment.php
class LeadAssignment
{
    private $conn;
    private $table_name = "lead_assignments";

    public $id;
    public $lead_id;
    public $provider_id;
    public $created_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET id=:id, lead_id=:lead_id, provider_id=:provider_id";

        $stmt = $this->conn->prepare($query);

        $this->id = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":lead_id", $this->lead_id);
        $stmt->bindParam(":provider_id", $this->provider_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
