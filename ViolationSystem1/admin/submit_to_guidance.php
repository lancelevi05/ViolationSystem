<?php
include("../config/db_connect.php");

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $update = "UPDATE violation SET status_id = 3 WHERE vi_id = $id";
    mysqli_query($conn, $update);

    header("Location: admin_advisorydashboard.php");
    exit();
}
?>
