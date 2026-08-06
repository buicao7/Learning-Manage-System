<?php

require_once "app/Models/Enrollment.php";

class EnrollmentController
{
    private $model;

    public function __construct($db)
    {
        $this->model = new Enrollment($db);
    }

    // Hiển thị danh sách + form thêm
    public function index()
    {
        $enrollments = $this->model->getAll();
        $students = $this->model->getStudents();
        $courses = $this->model->getCourses();

        require "app/Views/admin/enrollments.php";
    }

    // Lưu
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $this->model->create(
                $_POST['student_id'],
                $_POST['course_id']
            );

            header("Location:index.php?controller=enrollment");
            exit;
        }
    }

    // Xóa
    public function delete()
    {
        $this->model->delete($_GET['id']);

        header("Location:index.php?controller=enrollment");
        exit;
    }
}