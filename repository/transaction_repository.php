<?php
include 'base_repository.php';

class TransactionRepository extends BaseRepository {
    public function __construct($conn) {
        parent::__construct($conn, "Transaction");
    }

    public function create($id, $transaction_date, $transaction_type, $transaction_category, $description, $amount) {
        $stmt = $this->conn->prepare("
            INSERT INTO Transaction (id, transaction_date, transaction_type, transaction_category, description, amount)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssd", $id, $transaction_date, $transaction_type, $transaction_category, $description, $amount);
        return $stmt->execute();
    }

    public function update($id, $transaction_date, $transaction_type, $transaction_category, $description, $amount) {
        $stmt = $this->conn->prepare("
            UPDATE Transaction 
            SET transaction_date=?, transaction_type=?, transaction_category=?, description=?, amount=? 
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ssssds", $transaction_date, $transaction_type, $transaction_category, $description, $amount, $id);
        return $stmt->execute();
    }
}


?>