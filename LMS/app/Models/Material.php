<?php

class Material
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy tất cả tài liệu
    public function getAll()
    {
        $sql = "SELECT m.*, c.course_name
                FROM materials m
                JOIN courses c ON m.course_id = c.course_id
                ORDER BY m.material_id DESC";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy theo ID
    public function getById($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM materials WHERE material_id=?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Danh sách khóa học
    public function getCourses()
    {
        return $this->conn
            ->query("SELECT course_id,course_name FROM courses")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm tài liệu
    public function create($data)
    {
        $sql = "INSERT INTO materials
                (course_id,title,description,file_path)
                VALUES(?,?,?,?)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            $data['course_id'],
            $data['title'],
            $data['description'],
            $data['file_path']
        ]);
    }

    // Cập nhật
    public function update($id,$data)
    {
        $sql = "UPDATE materials
                SET course_id=?,
                    title=?,
                    description=?,
                    file_path=?
                WHERE material_id=?";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            $data['course_id'],
            $data['title'],
            $data['description'],
            $data['file_path'],
            $id
        ]);
    }

    // Xóa
    public function delete($id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM materials
             WHERE material_id=?"
        );

        return $stmt->execute([$id]);
    }
}