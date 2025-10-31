<?php
include_once 'base_repository.php';

class BudgetRepository extends BaseRepository {
    public function __construct($conn) {
        parent::__construct($conn, "Budget");
    }

    public function create($id, $name, $month, $year, $collected_amount, $remaining_amount, $target_amount, $percentage) {
        $stmt = $this->conn->prepare("
            INSERT INTO Budget (id, name, month, year, collected_amount, remaining_amount, target_amount, percentage)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssiidddi", $id, $name, $month, $year, $collected_amount, $remaining_amount, $target_amount, $percentage);
        return $stmt->execute();
    }

    public function update($id, $name, $month, $year, $collected_amount, $remaining_amount, $target_amount, $percentage) {
        $stmt = $this->conn->prepare("
            UPDATE Budget 
            SET name=?, month=?, year=?, collected_amount=?, remaining_amount=?, target_amount=?, percentage=? 
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ssiiddds", $name, $month, $year, $collected_amount, $remaining_amount, $target_amount, $percentage, $id);
        return $stmt->execute();
    }
}

?>