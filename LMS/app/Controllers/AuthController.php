<?php

session_start();

require_once __DIR__ . "/../Models/User.php";

class AuthController
{
    public function login()
    {
        if(isset($_POST['login']))
        {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $user = new User();

            $row = $user->login($email);

            if($row && password_verify($password,$row['password']))
            {
                $_SESSION['user_id']=$row['user_id'];
                $_SESSION['name']=$row['full_name'];
                $_SESSION['role']=$row['role'];

                if($row['role']=="admin")
                {
                    header("Location:index.php?page=admin");
                }
                elseif($row['role']=="lecturer")
                {
                    header("Location:index.php?page=lecturer");
                }
                else
                {
                    header("Location:index.php?page=student");
                }

                exit();
            }

            $error="Email hoặc Password không đúng.";
        }

        require_once __DIR__ . "/../Views/auth/login.php";
    }

    public function register()
    {
        if(isset($_POST['register']))
        {
            $user=new User();

            $user->register(
                $_POST['full_name'],
                $_POST['email'],
                $_POST['password'],
                $_POST['role']
            );

            header("Location:index.php?page=login");
            exit();
        }

        require_once __DIR__ . "/../Views/auth/register.php";
    }

    public function logout()
    {
        session_destroy();

        header("Location:index.php?page=login");

        exit();
    }
}