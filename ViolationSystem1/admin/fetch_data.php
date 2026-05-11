<?php
include("../config/db_connect.php");

$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';

if (!$month || !$year) {
    echo json_encode(['labels' => [], 'values' => []]);
    exit;
}

// Query: Count violations per day in the selected month and year
$sql = "
    SELECT DAY(`date`) AS day, COUNT(*) AS total
    FROM violation
    WHERE MONTH(`date`) = '$month' 
      AND YEAR(`date`) = '$year'
    GROUP BY DAY(`date`)
    ORDER BY day ASC
";

$result = mysqli_query($conn, $sql);

$labels = [];
$values = [];

while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['day'];
    $values[] = $row['total'];
}

echo json_encode(['labels' => $labels, 'values' => $values]);
?>
