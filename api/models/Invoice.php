<?php
// api/models/Invoice.php

class Invoice {
    private $conn;
    private $table_name = "invoices";

    public $id;
    public $provider_id;
    public $invoice_number;
    public $amount;
    public $currency;
    public $stripe_session_id;
    public $stripe_payment_id;
    public $stripe_customer_id;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Read invoices for a specific provider
     */
    public function readByProvider($provider_id) {
        $query = "SELECT id, invoice_number, amount, currency, status, created_at 
                  FROM " . $this->table_name . " 
                  WHERE provider_id = ? 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$provider_id]);
        return $stmt;
    }

    /**
     * Read one invoice by ID
     */
    public function readOne($id) {
        $query = "SELECT id, provider_id, invoice_number, amount, currency, status, created_at 
                  FROM " . $this->table_name . " 
                  WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Create a new invoice record
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET 
                    id = UUID(),
                    provider_id = :provider_id,
                    invoice_number = :invoice_number,
                    amount = :amount,
                    currency = :currency,
                    stripe_session_id = :stripe_session_id,
                    stripe_payment_id = :stripe_payment_id,
                    stripe_customer_id = :stripe_customer_id,
                    status = :status";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':provider_id', $this->provider_id);
        $stmt->bindParam(':invoice_number', $this->invoice_number);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':currency', $this->currency);
        $stmt->bindParam(':stripe_session_id', $this->stripe_session_id);
        $stmt->bindParam(':stripe_payment_id', $this->stripe_payment_id);
        $stmt->bindParam(':stripe_customer_id', $this->stripe_customer_id);
        $stmt->bindParam(':status', $this->status);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
