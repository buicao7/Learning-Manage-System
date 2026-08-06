<?php

require_once "app/Models/Grade.php";
require_once "app/Models/Notification.php";

class GradeController
{
    private $db;
    private $grade;
    private $notification;

    public function __construct($db)
    {
        $this->db = $db;
        $this->grade = new Grade($db);
        $this->notification = new Notification($db);
    }

    // Lecturer xem danh sách điểm
    public function index()
    {
        $grades = $this->grade->getAll();

        require "app/Views/lecturer/grades.php";
    }

    // Chấm điểm
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $submissionId = $_POST['submission_id'];

            $data = [
                "submission_id" => $submissionId,
                "score" => $_POST['score'],
                "feedback" => $_POST['feedback']
            ];

            // Kiểm tra đã có điểm chưa
            $check = $this->grade->getBySubmission($submissionId);

            if ($check) {
                $this->grade->update($submissionId, $data);
            } else {
                $this->grade->create($data);
            }

            // ====== TẠO THÔNG BÁO CHO SINH VIÊN ======
            $stmt = $this->db->prepare("
                SELECT
                    s.student_id,
                    a.title
                FROM submissions s
                JOIN assignments a
                    ON s.assignment_id = a.assignment_id
                WHERE s.submission_id = ?
            ");

            $stmt->execute([$submissionId]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {

                $message = "Your assignment \"" .
                            $result['title'] .
                            "\" has been graded.";

                $this->notification->create(
                    $result['student_id'],
                    $message
                );
            }
            // ========================================

            header("Location:index.php?controller=grade");
            exit;
        }
    }

    // Student xem điểm
    public function myGrades()
    {
        $studentId = $_SESSION['user']['user_id'];

        $grades = $this->grade->getStudentGrades($studentId);

        require "app/Views/student/grades.php";
    }

    // Xóa điểm
    public function delete()
    {
        if (isset($_GET['id'])) {

            $this->grade->delete($_GET['id']);
        }

        header("Location:index.php?controller=grade");
        exit;
    }
}