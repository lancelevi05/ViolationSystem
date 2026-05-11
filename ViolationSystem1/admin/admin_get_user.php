<?php
include("../config/db_connect.php");

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "No ID provided"]);
    exit();
}

$id = intval($_GET['id']);

$result = mysqli_query($conn, "SELECT * FROM users WHERE id = $id LIMIT 1");

if (mysqli_num_rows($result) == 1) {
    echo json_encode(mysqli_fetch_assoc($result));
} else {
    echo json_encode(["error" => "User not found"]);
}
?>
