<?php
include_once 'base_repository.php';

class SessionRepository extends BaseRepository
{
    public function __construct($conn)
    {
        parent::__construct($conn, "Session");
    }

    public function create($id, $user_id, $expired_at)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO Session (id, user_id, expired_at)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("sss", $id, $user_id, $expired_at);
        return $stmt->execute();
    }

    public function update($id, $user_id, $expired_at)
    {
        $stmt = $this->conn->prepare("
            UPDATE Session
            SET user_id=?, expired_at=?
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("sss", $user_id, $expired_at, $id);
        return $stmt->execute();
    }

    public function delete_by_id($id)
    {
        $stmt = $this->conn->prepare("
            UPDATE Session
            SET deleted_at=CURRENT_TIMESTAMP
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function get_by_id($id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM Session
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function get_by_user_id($user_id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM Session
            WHERE user_id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function delete_by_user_id($user_id)
    {
        $stmt = $this->conn->prepare("
            UPDATE Session
            SET deleted_at=CURRENT_TIMESTAMP
            WHERE user_id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $user_id);
        return $stmt->execute();
    }
}
?>
