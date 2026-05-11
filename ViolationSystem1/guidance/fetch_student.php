<?php
include("../config/db_connect.php");

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "No ID"]);
    exit;
}

$id = intval($_GET['id']);


$sql = "SELECT 
            st.*,
            sc.strandcourse,
            g.gender
        FROM shs_tbl st
        LEFT JOIN strandcourse_tbl sc ON st.idstrandcourse = sc.idstrandcourse
        LEFT JOIN gender_tbl g ON st.genid = g.genid
        WHERE st.id = $id";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode($row);
} else {
    echo json_encode(["error" => "Student not found"]);
}
