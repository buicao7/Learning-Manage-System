<?php

require_once "app/Models/Notification.php";

class NotificationController
{
    private $notification;

    public function __construct($db)
    {
        $this->notification = new Notification($db);
    }

    // Danh sách thông báo
    public function index()
    {
        $userId = $_SESSION['user']['user_id'];

        $notifications = $this->notification->getByUser($userId);

        require "app/Views/student/notifications.php";
    }

    // Đánh dấu đã đọc
    public function read()
    {
        $this->notification->markRead($_GET['id']);

        header("Location:index.php?controller=notification");
        exit;
    }

    // Xóa
    public function delete()
    {
        $this->notification->delete($_GET['id']);

        header("Location:index.php?controller=notification");
        exit;
    }
}