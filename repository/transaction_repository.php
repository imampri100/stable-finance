<?php
include_once 'base_repository.php';

class TransactionRepository extends BaseRepository {
    public function __construct($conn) {
        parent::__construct($conn, "Transaction");
    }

    public function create($id, $user_id, $transaction_date, $transaction_type, $transaction_category, $description, $amount) {
        $stmt = $this->conn->prepare("
            INSERT INTO Transaction (id, user_id, transaction_date, transaction_type, transaction_category, description, amount)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssssd", $id, $user_id, $transaction_date, $transaction_type, $transaction_category, $description, $amount);
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

    public function delete_by_id($id) {
        $stmt = $this->conn->prepare("
            UPDATE Transaction
            SET deleted_at=CURRENT_TIMESTAMP
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function delete_by_user_id_and_id($user_id, $id) {
        $stmt = $this->conn->prepare("
            UPDATE Transaction 
            SET deleted_at=NOW() 
            WHERE user_id=? AND id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ss", $user_id, $id);
        return $stmt->execute();
    }

    public function get_by_id($id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM Transaction
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function get_by_user_id($user_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM Transaction
            WHERE user_id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function get_by_user_id_and_id($user_id, $id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM Transaction
            WHERE user_id=? AND id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ss", $user_id, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>
