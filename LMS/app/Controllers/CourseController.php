<?php

require_once "app/Models/Course.php";

class CourseController
{
    private $course;

    public function __construct($db)
    {
        $this->course = new Course($db);
    }

    public function index()
    {
        $courses = $this->course->getAll();

        require "app/Views/admin/courses.php";
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            $this->course->create($_POST);

            header("Location: index.php?controller=course");
            exit;
        }
    }

    public function edit()
    {
        $id = $_GET['id'];

        $course = $this->course->getById($id);

        require "app/Views/admin/course_edit.php";
    }

    public function update()
    {
        if($_SERVER['REQUEST_METHOD']=="POST"){

            $id=$_POST['course_id'];

            $this->course->update($id,$_POST);

            header("Location:index.php?controller=course");
            exit;
        }
    }

    public function delete()
    {
        $id=$_GET['id'];

        $this->course->delete($id);

        header("Location:index.php?controller=course");
        exit;
    }
}