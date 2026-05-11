<?php
include("../config/db_connect.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "UPDATE college_tbl SET Archive = 1 WHERE id = $id";
    mysqli_query($conn, $sql);
}
$_SESSION['successMsg'] = "Student deleted successfully!";
header("Location: admin_college.php");
exit();
?>
