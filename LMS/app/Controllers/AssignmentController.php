<?php

require_once "app/Models/Assignment.php";

class AssignmentController
{
    private $db;
    private $assignment;

    public function __construct($db)
    {
        $this->db = $db;
        $this->assignment = new Assignment($db);
    }

    // Danh sách Assignment
    public function index()
    {
        $assignments = $this->assignment->getAll();

        require "app/Views/lecturer/assignments.php";
    }

    // Form thêm Assignment
    public function create()
    {
        $courses = $this->assignment->getCourses();

        require "app/Views/lecturer/create_assignment.php";
    }

    // Lưu Assignment
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $data = [
                "course_id"   => $_POST['course_id'],
                "title"       => trim($_POST['title']),
                "description" => trim($_POST['description']),
                "due_date"    => $_POST['due_date']
            ];

            $this->assignment->create($data);

            header("Location:index.php?controller=assignment");
            exit;
        }
    }

    // Form sửa
    public function edit()
    {
        $id = $_GET['id'];

        $assignment = $this->assignment->getById($id);

        $courses = $this->assignment->getCourses();

        require "app/Views/lecturer/edit_assignment.php";
    }

    // Cập nhật
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $id = $_POST['assignment_id'];

            $data = [
                "course_id"   => $_POST['course_id'],
                "title"       => trim($_POST['title']),
                "description" => trim($_POST['description']),
                "due_date"    => $_POST['due_date']
            ];

            $this->assignment->update($id, $data);

            header("Location:index.php?controller=assignment");
            exit;
        }
    }

    // Xóa
    public function delete()
    {
        if (isset($_GET['id'])) {

            $this->assignment->delete($_GET['id']);
        }

        header("Location:index.php?controller=assignment");
        exit;
    }

    // Chi tiết Assignment
    public function detail()
    {
        $id = $_GET['id'];

        $assignment = $this->assignment->getById($id);

        require "app/Views/student/assignment_detail.php";
    }

    // Student xem Assignment theo Course
    public function studentAssignments()
    {
        if (!isset($_GET['course_id'])) {
            header("Location:index.php");
            exit;
        }

        $courseId = $_GET['course_id'];

        $assignments = $this->assignment->getByCourse($courseId);

        require "app/Views/student/assignments.php";
    }
}