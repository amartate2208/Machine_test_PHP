<?php
class Member {
    private $conn;
    private $table_name = "Members";

    public $id;
    public $createdDate;
    public $name;
    public $parentId;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, parentId=:parentId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":parentId", $this->parentId);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function readRecursive($parentId = 0, $processed = []) {
        if (in_array($parentId, $processed)) {
            return [];
        }
        $processed[] = $parentId;

        $query = "SELECT * FROM " . $this->table_name . " WHERE parentId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $parentId);
        $stmt->execute();

        $members = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['children'] = $this->readRecursive($row['Id'], $processed);
            $members[] = $row;
        }
        return $members;
    }
}
