<?php
session_start();
include '../config/db_connect.php';
// Assume session check for admin
if (!isset($_SESSION['user_id'])) {
  header('Location: ../index.php');
  exit();

}

// Update current user's last activity
$current_user_id = $_SESSION['user_id'];
mysqli_query($conn, "UPDATE users SET last_activity = CURRENT_TIMESTAMP WHERE id = $current_user_id");

// Fetch all users with their online status
$query = "SELECT *, 
          CASE 
            WHEN last_activity >= NOW() - INTERVAL 5 MINUTE THEN 1 
            ELSE 0 
          END as is_online 
          FROM users 
          ORDER BY last_activity DESC";
$result = mysqli_query($conn, $query);
$name = $_SESSION['user_name'];
$profile = $_SESSION['profile'];
/* WHERE  reportedBy='$name'ORDER BY date DESC*/
$sql = "SELECT * FROM violation  JOIN violationstatus ON violation.status_id = violationstatus.status_id
WHERE  reportedBy='$name'ORDER BY date DESC;

";
$result = mysqli_query($conn, $sql);


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link href="../assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap JS -->
  <script src="../assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>
</head>

<body>

  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="#" style="color:white"><?php echo htmlspecialchars($name) ?></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarScroll">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="teacher_dashboard.php" style="color:white">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="report_violation.php" style="color:white">File Report</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../logout.php" style="color:white">Log Out</a>
          </li>
        </ul>
        <div class="d-flex">
          <img src="<?php echo htmlspecialchars($profile) ?>"
            style="height: 50px; width: 50px; object-fit: cover; border-radius: 50%">
        </div>
      </div>
    </div>
  </nav>
  <!---REPORT CARD-->
  <!-- REPORT CARD -->
  <div class="container my-5">
    <h1>My Reports</h1>
    <div class="row g-4">
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
            <div class="card h-200">
              <img src="<?php echo $row['evidence']; ?>" alt="Evidence Image" class="card-image">
              <div class="card-content">
                <h2 class="card-title" style="background: linear-gradient(to right, #41E09B, #4AC2D2, #5979F2);
-webkit-background-clip: text;
-webkit-text-fill-color: transparent;"><b>Name:</b>     <?php echo $row['person']; ?></h2>
                <p class="card-description" style="color:white">Location: <?php echo $row['location']; ?></p>
                <p class="card-description" style="color:white">Description: <?php echo $row['description']; ?></p>
                <p class="card-description" style="color:white">Type Violation: <?php echo $row['typeviolation']; ?></p>
                <p class="card-description" style="color:white">Case Status: <?php echo $row['vio_stats']; ?></p>
                <p class="card-more" style="color:white">Report Submitted: <?php echo $row['date']; ?></p>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-center text-muted">No reports found.</p>
      <?php endif; ?>
    </div>
  </div>
  <!---/////REPORT CARD-->
</body>

</html>

<style>
  body {
    font-family: Arial, sans-serif;
    margin: 0;

    background-color: rgb(26, 26, 26);

    /*
  background-color: rgb(26, 26, 26); */
    background: url(../img/bg.jpg);
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover;

  }

  .card-container {

    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    max-width: 1200px;
    width: 100%;
    display: flex;
    justify-content: left;
    align-items: flex-start;
    min-height: 100vh;
  }

  .card {

    background-color: rgba(26, 26, 26, 1);
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    max-width: 400px;
    width: 110%;
    transition: transform 0.3s ease;

  }

  .card:hover {
    transform: translateY(-5px);
  }

  .card-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
  }

  .card-content {
    padding: 20px;
  }

  .card-title {
    font-size: 1.5em;
    margin: 0 0 10px 0;
    color: #333;
  }

  .card-description {
    font-size: 1em;
    color: #666;
    margin: 0 0 15px 0;
    line-height: 1.5;
  }

  .card-more {
    font-size: 0.9em;
    color: #999;
    margin: 0;
  }

  /* Responsive adjustments */
  @media (max-width: 1000px) {
    .card-container {

      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      max-width: 1200px;
      width: 100%;
      display: column;
      justify-content: left;
      align-items: flex-start;
      min-height: 100vh;

      display: column;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
    }

    .card-content {
      padding: 15px;
    }
  }
</style>