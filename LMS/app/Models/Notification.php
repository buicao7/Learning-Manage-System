<?php

class Notification
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy thông báo theo user
    public function getByUser($userId)
    {
        $stmt = $this->conn->prepare(
            "SELECT *
             FROM notifications
             WHERE user_id=?
             ORDER BY created_at DESC"
        );

        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm thông báo
    public function create($userId, $message)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO notifications(user_id,message)
             VALUES(?,?)"
        );

        return $stmt->execute([
            $userId,
            $message
        ]);
    }

    // Đánh dấu đã đọc
    public function markRead($id)
    {
        $stmt = $this->conn->prepare(
            "UPDATE notifications
             SET is_read=1
             WHERE notification_id=?"
        );

        return $stmt->execute([$id]);
    }

    // Xóa
    public function delete($id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM notifications
             WHERE notification_id=?"
        );

        return $stmt->execute([$id]);
    }

    // Đếm số chưa đọc
    public function unreadCount($userId)
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) total
             FROM notifications
             WHERE user_id=?
             AND is_read=0"
        );

        $stmt->execute([$userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
public function create($userId, $message)
{
    $stmt = $this->conn->prepare("
        INSERT INTO notifications(user_id, message)
        VALUES(?, ?)
    ");

    return $stmt->execute([
        $userId,
        $message
    ]);
}