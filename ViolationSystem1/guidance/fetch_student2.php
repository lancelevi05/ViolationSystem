<?php
include("../config/db_connect.php");

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "No ID"]);
    exit;
}

$id = intval($_GET['id']);


$sql = "SELECT 
            ct.*,
            sc.strandcourse,
            g.gender
        FROM college_tbl ct
        LEFT JOIN strandcourse_tbl sc ON ct.idstrandcourse = sc.idstrandcourse
        LEFT JOIN gender_tbl g ON ct.genid = g.genid
        WHERE ct.id = $id";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode($row);
} else {
    echo json_encode(["error" => "Student not found"]);
}
