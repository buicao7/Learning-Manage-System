<?php

require_once "app/Models/Submission.php";

class SubmissionController
{
    private $submission;

    public function __construct($db)
    {
        $this->submission = new Submission($db);
    }

    // Lecturer xem bài nộp
    public function index()
    {
        $submissions = $this->submission->getAll();

        require "app/Views/lecturer/submissions.php";
    }

    // Student nộp bài
    public function store()
    {
        if($_SERVER['REQUEST_METHOD']=="POST")
        {
            $file = "";

            if(!empty($_FILES['file']['name']))
            {
                $file = time()."_".$_FILES['file']['name'];

                move_uploaded_file(
                    $_FILES['file']['tmp_name'],
                    "uploads/submissions/".$file
                );
            }

            $data = [

                "assignment_id"=>$_POST['assignment_id'],

                "student_id"=>$_SESSION['user']['user_id'],

                "file_path"=>$file

            ];

            $this->submission->create($data);

            header("Location:index.php?controller=student");
            exit;
        }
    }

    // Download bài nộp
    public function download()
    {
        $submission = $this->submission->getById($_GET['id']);

        $path = "uploads/submissions/".$submission['file_path'];

        if(file_exists($path))
        {
            header("Content-Type: application/octet-stream");
            header(
                "Content-Disposition: attachment; filename=".$submission['file_path']
            );

            readfile($path);
        }
    }

    // Xóa
    public function delete()
    {
        $this->submission->delete($_GET['id']);

        header("Location:index.php?controller=submission");
        exit;
    }
}