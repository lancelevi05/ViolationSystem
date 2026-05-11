<?php
include("../config/db_connect.php");

$search = $_GET['search'] ?? '';

if (empty($search)) {
    echo json_encode([]);
    exit;
}

$search = "%$search%";

$sql = "
    SELECT id, usn, fname, mname, lname, strandcourse_tbl.strandcourse, 
           s.idstrandcourse, s.glevel, s.section
    FROM shs_tbl s
    LEFT JOIN strandcourse_tbl ON s.idstrandcourse = strandcourse_tbl.idstrandcourse
    WHERE s.usn LIKE ?
       OR s.fname LIKE ?
       OR s.lname LIKE ?
       OR s.mname LIKE ?

    UNION

    SELECT id, usn, fname, mname, lname, strandcourse_tbl.strandcourse,
           c.idstrandcourse, c.glevel, c.section
    FROM college_tbl c
    LEFT JOIN strandcourse_tbl ON c.idstrandcourse = strandcourse_tbl.idstrandcourse
    WHERE c.usn LIKE ?
       OR c.fname LIKE ?
       OR c.lname LIKE ?
       OR c.mname LIKE ?

    LIMIT 20
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", 
    $search, $search, $search, $search,   // SHS placeholders
    $search, $search, $search, $search    // COLLEGE placeholders
);

$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);
?>
