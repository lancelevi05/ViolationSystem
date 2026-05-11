<?php
include("../config/db_connect.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "UPDATE shs_tbl SET Archive = 1 WHERE id = $id";
    mysqli_query($conn, $sql);
}

header("Location: admin_seniorhigh.php");
exit();
?>
