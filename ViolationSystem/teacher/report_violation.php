<?php
session_start();
include('../config/db_connect.php'); // adjust path if needed

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: ../index.php');
  exit();
}

$name = $_SESSION['user_name'];
$profile = $_SESSION['profile'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $person = mysqli_real_escape_string($conn, $_POST['person']);
  $location = mysqli_real_escape_string($conn, $_POST['location']);
  $description = mysqli_real_escape_string($conn, $_POST['description']);
  $typeviolation = mysqli_real_escape_string($conn, $_POST['typeviolation']);
  $reportedBy = $name;

  //$date = date('Y-m-d H:i:s'); // current date/time

  // Check if "Other" was selected
  if ($typeviolation == "Other" && !empty($_POST['otherViolation'])) {
    $typeviolation = mysqli_real_escape_string($conn, $_POST['otherViolation']);
  }

  // Create upload folder if not existing
  $targetDir = "../uploads/evidences/";
  if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
  }

  $evidencePath = "../img/defaultIMG.png";

  // Check if file is uploaded
  if (!empty($_FILES["image"]["name"])) {
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Allow only specific file types
    $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
    if (in_array($fileType, $allowTypes)) {
      // Clean the person's name to make it filename-safe
      $cleanPerson = preg_replace("/[^a-zA-Z0-9_-]/", "_", strtolower($person));

      // Create new filename (e.g. "juan_dela_cruz_20251031_103045.jpg")
      $newFileName = $cleanPerson . "_" ./* date("Ymd_His") .*/ "." . $fileType;

      $targetFilePath = $targetDir . $newFileName;
      if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        // Save relative path (for display)
        $evidencePath = "../uploads/evidences/" . $newFileName;
      } else {
        echo "<script>alert('Error uploading image.');</script>";
      }
    } else {
      echo "<script>alert('Invalid file type. Please upload an image.');</script>";
    }
  }

  // Insert into database
  $sql = "INSERT INTO violation (person, location, typeviolation, description, evidence, reportedBy, status_id)
            VALUES ('$person', '$location', '$typeviolation', '$description', '$evidencePath', '$reportedBy',1)";

  if (mysqli_query($conn, $sql)) {
    header('Location: success.php');

    //     echo "<script>alert('Violation reported successfully!'); window.location='report_violation.php';</script>";
  } else {
    echo "<script>alert('Database error: " . mysqli_error($conn) . "');</script>";
  }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link href="../assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="../assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous"></script>
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
  <br>

  <section class="vh-100">

    <div class="container-fluid h-custom" >

      <div class="row d-flex justify-content-center align-items-center h-100" >
        <div class="col-md-9 col-lg-6 col-xl-5">
          <img src="../img/reportVio.png" class="img-fluid" alt="Sample image" style="height:50%;" >
        </div>

        <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1"style="
    background-color: rgba(26, 26, 26, 1);
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
          <h2 style="background: linear-gradient(to right, #41E09B, #4AC2D2, #5979F2);
-webkit-background-clip: text;
-webkit-text-fill-color: transparent;">Report Form</h2>
          <br>
          <form action="report_violation.php" method="POST" enctype="multipart/form-data">

            <!-- Name of Person input -->
            <div data-mdb-input-init class="form-outline mb-4">
              <input type="person" id="person" class="form-control form-control-lg" placeholder="Enter a valid name"
                name="person" required/>
              <label class="form-label" for="person" style="background: linear-gradient(to right, #41E09B, #4AC2D2, #5979F2);
-webkit-background-clip: text;
-webkit-text-fill-color: transparent;"><b>Person</b></label>
            </div>

            <!-- location input -->
            <div data-mdb-input-init class="form-outline mb-4">
              <input type="location" id="location" class="form-control form-control-lg" placeholder="Enter a location"
                name="location"  required/>
              <label class="form-label" for="location" style="background: linear-gradient(to right, #41E09B, #4AC2D2, #5979F2);-webkit-background-clip: text;-webkit-text-fill-color: transparent;">
                Location</label>
            </div>



            <!-- description input -->
            <div data-mdb-input-init class="form-outline mb-3">
              <input type="comment" id="form3Example4" class="form-control form-control-lg"
                placeholder="Enter description" name="description" required/>
              <label class="form-label" for="form3Example4" style="background: linear-gradient(to right, #41E09B, #4AC2D2, #5979F2);-webkit-background-clip: text;-webkit-text-fill-color: transparent;">
                Description</label>
            </div>

            <!-- types violation input -->
            <div data-mdb-input-init class="form-outline mb-3">


              <select id="typesViolation" class="form-control form-control-lg" name="typeviolation"
                onchange="checkOtherOption(this)" required>
                <option value="Tardiness">Tardiness</option>
                <option value="Misconduct">Misconduct</option>
                <option value="Dress Code">Dress Code</option>
                <option value="Other">Other</option>
              </select>
              <!-- Hidden input for custom violation -->
              <input type="text" id="otherViolation" name="otherViolation" class="form-control mt-2"
                placeholder="Please specify" style="display:none;">
              <label class="form-label" for="form3Example4" style="background: linear-gradient(to right, #41E09B, #4AC2D2, #5979F2);-webkit-background-clip: text;-webkit-text-fill-color: transparent;">
                <b>Types of violation </b></label>
            </div>

            <div class="d-flex justify-content-between align-items-left">
              <!-- Checkbox -->
              <div class="form-check mb-0">


                <!-- image input -->
                <label for="image" class="form-label" required style="background: linear-gradient(to right, #41E09B, #4AC2D2, #5979F2);-webkit-background-clip: text;-webkit-text-fill-color: transparent;">Upload Evidence (optional)</label>
                <input class="form-control" type="file" id="image" accept="image/*" name="image">

              </div>
            </div>

            <div class="text-center text-lg-start mt-4 pt-2">
              <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn  btn-lg"
                style="padding-left: 2.5rem; padding-right: 2.5rem; background-color:rgba(50, 160, 111, 1); color:white;">Submit Report</button>

            </div>

          </form>
        </div>
      </div>
    </div>

  </section>


  <script>
    function checkOtherOption(select) {
      var otherInput = document.getElementById('otherViolation');
      if (select.value === "Other") {
        otherInput.style.display = "block";
        otherInput.required = true;
      } else {
        otherInput.style.display = "none";
        otherInput.required = false;
        otherInput.value = ""; // clear input
      }
    }


    function previewFile(event) {
      const preview = document.getElementById('previewImage');
      const file = event.target.files[0];
      if (file) {
        preview.src = URL.createObjectURL(file);
      } else {
        preview.src = "";
      }
    }
  </script>
</body>

</html>

<style>
  
  .divider:after,
  .divider:before {
    content: "";
    flex: 1;
    height: 1px;
    background: #eee;
  }

  .h-custom {
    height: calc(100% - 73px);
  }

  @media (max-width: 450px) {
    .h-custom {
      height: 100%;
    }
  }

  body {
     background-color:rgb(26, 26, 26);
  
  /*
  background-color: rgb(26, 26, 26); */
  background: url(../img/bg.jpg);
   background-repeat: no-repeat;
  background-attachment: fixed; 
  background-size: cover;
  }
</style>