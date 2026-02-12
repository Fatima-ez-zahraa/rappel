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
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function readByProvider($provider_id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE provider_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $provider_id);
        $stmt->execute();
        return $stmt;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    id = :id,
                    provider_id = :provider_id,
                    client_name = :client_name,
                    project_name = :project_name,
                    amount = :amount,
                    items_count = :items_count,
                    status = :status";

        $stmt = $this->conn->prepare($query);

        // Bind
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':provider_id', $this->provider_id);
        $stmt->bindParam(':client_name', $this->client_name);
        $stmt->bindParam(':project_name', $this->project_name);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':items_count', $this->items_count);
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
