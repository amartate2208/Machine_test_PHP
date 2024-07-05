<?php
include_once 'db_connect.php';
include_once 'member.class.php';

if (isset($_POST['name']) && isset($_POST['parent'])) {
    $database = new Database();
    $db = $database->getConnection();
    $member = new Member($db);

    $member->name = $_POST['name'];
    $member->parentId = $_POST['parent'];

    if ($member->create()) {
        $response = array(
            "id" => $db->lastInsertId(),
            "name" => $member->name,
            "parentId" => $member->parentId
        );
        echo json_encode($response);
    } else {
        echo json_encode(array("message" => "Unable to add member."));
    }
} else {
    echo json_encode(array("message" => "Invalid input."));
}
