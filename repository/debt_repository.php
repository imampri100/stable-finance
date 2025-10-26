<?php

class DebtRepository extends BaseRepository {
    public function __construct($conn) {
        parent::__construct($conn, "Debt");
    }

    public function create($id, $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount, $percentage) {
        $stmt = $this->conn->prepare("
            INSERT INTO Debt (id, name, start_date, end_date, collected_amount, remaining_amount, target_amount, percentage)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssdddd", $id, $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount, $percentage);
        return $stmt->execute();
    }

    public function update($id, $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount, $percentage) {
        $stmt = $this->conn->prepare("
            UPDATE Debt 
            SET name=?, start_date=?, end_date=?, collected_amount=?, remaining_amount=?, target_amount=?, percentage=? 
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("sssdddds", $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount, $percentage, $id);
        return $stmt->execute();
    }
}

?>