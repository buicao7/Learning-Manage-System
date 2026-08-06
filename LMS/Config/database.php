<?php

class Database
{
    private $host = "sql310.infinityfree.com";
    private $dbname = "if0_42573555_lms";
    private $username = "if0_42573555";
    private $password = "Cao13072006";

    private $conn;

    public function connect()
    {
        if($this->conn==null)
        {
            $this->conn=new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->dbname
            );

            if($this->conn->connect_error)
            {
                die("Database Connection Failed : ".$this->conn->connect_error);
            }

            $this->conn->set_charset("utf8");
        }

        return $this->conn;
    }
}