<?php
include_once 'base_repository.php';

class SavingRepository extends BaseRepository {
    public function __construct($conn) {
        parent::__construct($conn, "Saving");
    }

    public function create($id, $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount) {
        $stmt = $this->conn->prepare("
            INSERT INTO Saving (id, name, start_date, end_date, collected_amount, remaining_amount, target_amount)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssddd", $id, $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount);
        return $stmt->execute();
    }

    public function update($id, $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount) {
        $stmt = $this->conn->prepare("
            UPDATE Saving 
            SET name=?, start_date=?, end_date=?, collected_amount=?, remaining_amount=?, target_amount=? 
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("sssddds", $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount, $id);
        return $stmt->execute();
    }
}

?>