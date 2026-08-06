<?php

require_once "app/Models/Material.php";

class MaterialController
{
    private $material;

    public function __construct($db)
    {
        $this->material = new Material($db);
    }

    public function index()
    {
        $materials = $this->material->getAll();
        $courses = $this->material->getCourses();

        require "app/Views/lecturer/materials.php";
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $file = "";

            if (!empty($_FILES['file']['name'])) {

                $file = time() . "_" . $_FILES['file']['name'];

                move_uploaded_file(
                    $_FILES['file']['tmp_name'],
                    "uploads/materials/" . $file
                );
            }

            $data = [
                "course_id" => $_POST['course_id'],
                "title" => $_POST['title'],
                "description" => $_POST['description'],
                "file_path" => $file
            ];

            $this->material->create($data);

            header("Location:index.php?controller=material");
            exit;
        }
    }

    public function delete()
    {
        $this->material->delete($_GET['id']);

        header("Location:index.php?controller=material");
        exit;
    }
}