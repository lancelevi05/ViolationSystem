<?php
session_start();
include("../config/db_connect.php");

// Session check
if (!isset($_SESSION['user_id'])) {
  header('Location: ../index.php');
  exit();
}




$name = $_SESSION['user_name'];
$profile = $_SESSION['profile'];


$successMsg = $_SESSION['successMsg'] ?? '';
$errorMsg = $_SESSION['errorMsg'] ?? '';

// Clear messages after displaying
unset($_SESSION['successMsg']);
unset($_SESSION['errorMsg']);
// Update current user's last activity
$current_user_id = $_SESSION['user_id'];
mysqli_query($conn, "UPDATE users SET last_activity = CURRENT_TIMESTAMP WHERE id = $current_user_id");

// Count new violations for this teacher's advisory students
$newViolatorQuery = "
    SELECT COUNT(*) AS new_count
    FROM violation v
    INNER JOIN advisory_tbl a 
        ON a.idstrandcourse = v.idstrandcourse
        AND a.glevel = v.glevel
        AND a.section = v.section
    WHERE a.adviser_id = '$current_user_id'
      AND v.status_id = 1      -- Pending/new
      AND v.seen_by_adviser = 0  -- Only unread
";
$newViolatorResult = mysqli_query($conn, $newViolatorQuery);
$newViolatorRow = mysqli_fetch_assoc($newViolatorResult);
$newViolatorCount = $newViolatorRow['new_count'];


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $usn = mysqli_real_escape_string($conn, $_POST['usn']);
   $fname = mysqli_real_escape_string($conn, $_POST['fname']);
  $mname = mysqli_real_escape_string($conn, $_POST['mname']);
  $lname = mysqli_real_escape_string($conn, $_POST['lname']);
  $location = mysqli_real_escape_string($conn, $_POST['location']);
  $description = mysqli_real_escape_string($conn, $_POST['description']);
  $typeviolation = mysqli_real_escape_string($conn, $_POST['typeviolation']);
  $idstrandcourse = mysqli_real_escape_string($conn, $_POST['idstrandcourse']);
  $glevel = mysqli_real_escape_string($conn, $_POST['glevel']);
  $section = mysqli_real_escape_string($conn, $_POST['section']);
  $reportedBy = $name;
  // Combine into one name string
  //$person = trim($lname . ', ' . $fname . ' ' . (empty($mname) ? '' : substr($mname, 0, 1) . '.'));


  /* ✅ STEP 1: Verify if violator exists in student database
  $checkQuery = "SELECT * FROM college_tbl WHERE  lname = '$lname' AND fname = '$fname'";
  $checkQuery = "SELECT * FROM shs_tbl WHERE  lname = '$lname' AND fname = '$fname'";
  $checkResult = mysqli_query($conn, $checkQuery); */

// Check SHS
$checkSHS = mysqli_query($conn, 
    "SELECT id FROM shs_tbl WHERE lname='$lname' AND fname='$fname'"
);

// Check COLLEGE
$checkCollege = mysqli_query($conn, 
    "SELECT id FROM college_tbl WHERE lname='$lname' AND fname='$fname'"
);

// Determine where the student exists
$studentTable = "";
$studentID = "";

if (mysqli_num_rows($checkSHS) > 0) {
    $row = mysqli_fetch_assoc($checkSHS);
    $studentTable = "shs_tbl";
    $studentID = $row['id'];

} elseif (mysqli_num_rows($checkCollege) > 0) {
    $row = mysqli_fetch_assoc($checkCollege);
    $studentTable = "college_tbl";
    $studentID = $row['id'];

} else {

     $_SESSION['errorMsg'] = "The violator is not found in the student records!";
    
}

 
  // Check if "Other" was selected
  if ($typeviolation == "Other" && !empty($_POST['otherViolation'])) {
    $typeviolation = mysqli_real_escape_string($conn, $_POST['otherViolation']);
  }

  // Create upload folder if not existing
  $targetDir = "../uploads/evidences/";
  if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
  }

  //$evidencePath = "../img/defaultIMG.png";

  // Check if file is uploaded
  if (!empty($_FILES["image"]["name"])) {
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Allow only specific file types
    $allowTypes = array('jpg', 'jpeg', 'png', 'gif','jfif','webp');
    if (in_array($fileType, $allowTypes)) {
      // Clean the person's name to make it filename-safe
      $cleanPerson = preg_replace("/[^a-zA-Z0-9_-]/", "_", strtolower($person));

      // Add date and time (YearMonthDay_HourMinuteSecond)
$timestamp = date("Ymd_His");
      // Create new filename (e.g. "juan_dela_cruz_20251031_103045.jpg")
      $newFileName = $cleanPerson . "_" .$timestamp. "." . $fileType;

      $targetFilePath = $targetDir . $newFileName;
      if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        // Save relative path (for display)
        $evidencePath = "../uploads/evidences/" . $newFileName;
      } else {
       
         $_SESSION['errorMsg'] = "Error uploading image!";
        
      }
    } else {
 
      $_SESSION['errorMsg'] = "Invalid file type. Please upload an image.!";
    }
  }

  // Insert into database
  $sql = "INSERT INTO violation (usn, lname,fname,mname, location, typeviolation, description, evidence, reportedBy, status_id, idstrandcourse, glevel, section)
            VALUES ('$usn','$lname','$fname','$mname', '$location', '$typeviolation', '$description', '$evidencePath', '$reportedBy',1, '$idstrandcourse', '$glevel' , '$section')";

  if (mysqli_query($conn, $sql)) {
     // Update the student's vio_record to 1
    mysqli_query($conn, 
        "UPDATE $studentTable SET vio_record = 1 WHERE id = '$studentID'"
    );

    // Set success message in session
    $_SESSION['successMsg'] = "Student added successfully!";
    // Redirect to the next page
    header("Location: admin_myreport.php");
    exit();
  } else {
    /*
    $_SESSION['errorMsg'] = "Error: " . mysqli_error($conn); */
         $_SESSION['errorMsg'] = "The violator is not found in the student records!";
    header("Location: report_violation.php"); // redirect even if error
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
  <link rel="stylesheet" href="../assets/style.css">

  <!-- Linking Google fonts for icons -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>

<body style="">

  <!-- Navbar -->
  <nav class="site-nav">
    <button class="sidebar-toggle">
      <span class="material-symbols-rounded">menu</span>
    </button>
  </nav>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar collapsed">
      <!-- Sidebar header -->
      <div class="sidebar-header">
       <div style="display: flex; align-items: center; gap: 8px;">
        <img src="<?php echo $profile; ?>" style="width:46px;  height: 46px;
  display: block;
  object-fit: contain;
  border-radius: 50%; transition: opacity 0.4s ease;" id="profilePic" alt="Profile" />
        <span id="userName" style="color:var(--color-text-primary)"><?php echo htmlspecialchars($name); ?></span>
    </div>

        <button class="sidebar-toggle">

          <span class="material-symbols-rounded">chevron_left</span>
        </button>
        <!-- Dropdown -->
        <div class="dropdown-menu" id="dropdownMenu">
        
          <a href="../logout.php">Logout</a>
        </div>

      </div>

      <div class="sidebar-content">
        <!-- Search Form -->
        <form action="#" class="search-form">
          <span class="material-symbols-rounded">search</span>
          <input type="search" placeholder="Search..." required />
        </form>
        <!-- Sidebar Menu -->
        <ul class="menu-list">


        <li class="menu-item">
            <a href="admin_dashboard.php" class="menu-link ">
              <span class="material-symbols-rounded">dashboard</span>
              <span class="menu-label">Dashboard</span>
            </a>
          </li>
          <hr>
<li class="menu-item">
            <a href="admin_myreport.php" class="menu-link ">
              <span class="material-symbols-rounded">report</span>
              <span class="menu-label">My reports</span>
            </a>
          </li>
          <li class="menu-item">
            <a href="report_violation.php" class="menu-link active">
              <span class="material-symbols-rounded">forms_add_on</span>
              <span class="menu-label">File Report</span>

            </a>
          </li>

          <li class="menu-item">
    <a href="admin_advisorydashboard.php" class="menu-link">
        <span class="material-symbols-rounded">group</span>
        <span class="menu-label">Advisory</span>
        <?php if ($newViolatorCount > 0): ?>
            <span class="badge" style="
                background-color: red; 
                color: white; 
                font-size: 12px; 
                border-radius: 50%; 
                padding: 2px 7px; 
                margin-left: 5px;">
                <?= $newViolatorCount ?>
            </span>
            <span style="font-size: 12px; color: var(--color-text-secondary); margin-left:5px;">
                New violator<?= $newViolatorCount > 1 ? 's' : '' ?>!
            </span>
        <?php endif; ?>
    </a>
</li>
<hr>

          <li class="menu-item">
            <a href="admin_summary.php" class="menu-link">
              <span class="material-symbols-rounded">insert_chart</span>
              <span class="menu-label">Analytics</span>
            </a>
          </li>

          <li class="menu-item">
            <a href="admin_violation.php" class="menu-link ">
              <span class="material-symbols-rounded">warning</span>
              <span class="menu-label">Violation Student List</span>
            </a>
          </li>
         
          

          <li class="menu-item has-dropdown">
            <a href="#" class="menu-link dropdown-toggle">
              <span class="material-symbols-rounded">group</span>
              <span class="menu-label">Student/teacher</span>
              <span class="material-symbols-rounded arrow">expand_more</span>
            </a>
            <ul class="submenu">
              <li class="menu-link" ><a href="admin_seniorhigh.php" class="menu-label" style="color:var(--color-text-primary)">Senior High</a></li>
              <li class="menu-link"><a href="admin_college.php" style="color:var(--color-text-primary)">College</a></li>
              <li class="menu-link"><a href="user_management.php" style="color:var(--color-text-primary)">User Logs</a></li>
            </ul>

          </li>
          

        </ul>
      </div>
      <!-- Sidebar Footer -->
      <div class="sidebar-footer">
        <button class="theme-toggle">
          <div class="theme-label">
            <span class="theme-icon material-symbols-rounded">dark_mode</span>
            <span class="theme-text">Dark Mode</span>
          </div>
          <div class="theme-toggle-track">
            <div class="theme-toggle-indicator"></div>
          </div>
        </button>
      </div>
    </aside>
    <!-- Site main content -->
  
        
     
    <div class="main-content" style="
  overflow: hidden auto;
  scrollbar-width: thin;">
  <?php if (!empty($successMsg)): ?>
        <div class="message success">
          <?= $successMsg ?>
        </div>
      <?php elseif (!empty($errorMsg)): ?>
        <div class="message error">
          <?= $errorMsg ?>
        </div>
          <?php endif; ?>
     <h1 class="card">File Report</h1>
      <br>


      <section class="report-section" >
        <div class="report-container" >

          <!-- Left Image -->
          <div class="report-image" >
            <img src="../img/reportVIO.png" alt="Report Image" style="width:500px">
          </div>

          <!-- Right Form -->
          <div class="report-form">
            <h2 class="report-title">Report Form</h2>

            <form action="report_violation.php" method="POST" enctype="multipart/form-data">

              
              <!-- Student Search Bar -->
<div class="form-group">
    <label class="form-label">Search Student</label>
    <input type="text" id="studentSearch" placeholder="Search by Lastname, Firstname, Middlename (optional)">
    <div id="searchResults" class="search-results"></div>
</div>
<!-- usn Input -->
              <div class="form-group" STYLE="">
                <label for="usn" class="form-label">USN</label>
                <input type="text" id="usn" name="usn" placeholder="Enter student's USN" readonly >

              </div>
              <div class="divrow">

               
                <!-- lname Input -->
                <div class="form-group">
                  <label for="lname" class="form-label">Last Name</label>
                  <input readonly type="text" id="lname" name="lname" placeholder="Enter valid Last name" required>
                </div>

                 <!-- fname Input -->
                <div class="form-group">
                  <label for="fname" class="form-label">first Name</label>
                  <input readonly type="text" id="fname" name="fname" placeholder="Enter valid First name" required>
                </div>

                <!-- lname Input -->
                <div class="form-group">
                  <label for="mname" class="form-label">Middle Name</label>
                  <input readonly type="text" id="mname" name="mname" placeholder="Enter valid middle name">
                </div>
                
              </div>


               <div class="divrow">

                <!-- strandcourse Input -->
                <div class="form-group">
                  <label for="idstrandcourse" class="form-label">strandcourse</label>
                  <input type="text" id="strandcourse"  placeholder="Enter valid strand/course" readonly>
                  <input type="text" id="idstrandcourse" name="idstrandcourse"  readonly style="display:none">
                </div>

                 <!-- Glevel Input -->
                <div class="form-group">
                  <label for="glevel" class="form-label">Grade/YEAR LEVEL</label>
                  <input type="text" id="glevel" name="glevel" placeholder="Enter valid First name" readonly>
                </div>

                <!-- SECTION Input -->
                <div class="form-group">
                  <label for="section" class="form-label">Section</label>
                  <input type="text" id="section" name="section" placeholder="Enter valid First name" readonly>
                </div>

                           

              </div>

              <!-- Location Input -->
                <div class="form-group">
                  <label for="location" class="form-label">Location</label>
                  <input type="text" id="location" name="location" placeholder="Enter valid Location">
                </div>

                <!-- Description Input -->
                <div class="form-group">
                  <label for="description" class="form-label">Description</label>
                  <input type="text" id="description" name="description" placeholder="Enter valid Description" required>
                </div>

                <div class="form-group">
                  <label for="" class="form-label">Types of Violation</label>
                  <select id="typesViolation" class="form-control form-control-lg" name="typeviolation"
                onchange="checkOtherOption(this)" required>
                <option value="Tardiness">Tardiness</option>
                <option value="Misconduct">Misconduct</option>
                <option value="Dress Code">Dress Code</option>
                <option value="Other">Other</option>
              </select>
              <!-- Hidden input for custom violation -->
               <br>
              <input type="text" id="otherViolation" name="otherViolation" class=""
                placeholder="Please specify" style="display:none;">
                </div>



                <!-- IMG Input -->
                <div class="form-group">
                  <label for="image" class="form-label" style="background: linear-gradient(to right, #41E09B, #4AC2D2, #5979F2);-webkit-background-clip: text;-webkit-text-fill-color: transparent;">Upload Evidence</label>
                  <input type="file" id="image" name="image" accept="image/*" required>
                </div>




              <!-- Submit Button -->
              <div class="form-action">
                <button type="submit" class="submit-btn">Submit Report</button>
              </div>

            </form>
          </div>
        </div>
      </section>
    </div>
  </div>
  <script src="../js/main.js"></script>
  <script>
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
      toggle.addEventListener('click', e => {
        e.preventDefault();
        const parent = toggle.closest('.menu-item');
        parent.classList.toggle('active');
      });
    });

const searchInput = document.getElementById("studentSearch");
const searchResults = document.getElementById("searchResults");

searchInput.addEventListener("keyup", async () => {
    let value = searchInput.value.trim();
    if (value === "") {
        searchResults.style.display = "none";
        return;
    }

    const res = await fetch(`fetch_student_name.php?search=${value}`);
    const data = await res.json();

    searchResults.innerHTML = "";
    searchResults.style.display = "block";

    if (data.length === 0) {
        searchResults.innerHTML = "<div>No results found</div>";
        return;
    }

    data.forEach(student => {
        let div = document.createElement("div");
        div.textContent = ` ${student.usn} - ${student.lname}, ${student.fname} ${student.mname || ''} (${student.strandcourse})`;
        
        div.addEventListener("click", () => {
            // Auto-fill fields
            document.getElementById("usn").value = student.usn;
            document.getElementById("lname").value = student.lname;
            document.getElementById("fname").value = student.fname;
            document.getElementById("mname").value = student.mname;

            document.getElementById("strandcourse").value = student.strandcourse;
            document.getElementById("idstrandcourse").value = student.idstrandcourse;
            document.getElementById("glevel").value = student.glevel;
            document.getElementById("section").value = student.section;

            searchResults.style.display = "none";
            searchInput.value = "";
        });

        searchResults.appendChild(div);
    });
});



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

    const profilePic = document.getElementById('profilePic');
    const dropdownMenu = document.getElementById('dropdownMenu');

    // Toggle dropdown on profile click
    profilePic.addEventListener('click', () => {
      dropdownMenu.classList.toggle('show');
    });
  </script>
</body>
<style>
  .search-results {
    background: var(--color-bg-form);
    border: 1px solid #ccc;

    width: 100%;
    max-height: 200px;
    overflow-y: auto;
    display: none;
    z-index: 1000;
}

.search-results div {
    padding: 10px;
    cursor: pointer;
}

.search-results div:hover {
    background: #f0f0f0;
}
    .message {
    padding: 12px 20px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-size: 1rem;
    font-weight: 500;
    text-align: center;
  }

  .message.success {
    background-color: #4BB543;
    color: white;
  }

  .message.error {
    background-color: #FF4C4C;
    color: white;
  }
  /* Sidebar header container */
  .sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
  }

  /* Profile image */
  .profile-pic {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s ease;
  }

  .profile-pic:hover {
    transform: scale(1.05);
  }

  /* Dropdown menu styling */
  .dropdown-menu {
    position: absolute;
    top: 60px;
    left: 0;
    background: #ffffffff;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    width: 160px;
    overflow: hidden;
    opacity: 0;
    transform: translateY(-10px);
    pointer-events: none;
    transition: all 0.3s ease;
    z-index: 10;
  }

  .dropdown-menu.show {
    opacity: 1;
    transform: translateY(0);
    pointer-events: auto;
  }

  .dropdown-menu a {
    display: block;
    padding: 10px 15px;
    color: #333;
    text-decoration: none;
    transition: background 0.2s;
  }

  .dropdown-menu a:hover {
    background: #f1f1f1;
  }



  .divrow {
    display: flex;
    gap: 20px;
    /* space between first and last name */
  }

  .form-group {
    flex: 1;
    /* make both inputs take equal width */
  }

  /**----------------------------------------- */

  .form-group input {
    width: 100%;
    /* make input stretch to its container */
    padding: 8px;
    box-sizing: border-box;
  }

  /* Full height section */
  .report-section {

    align-items: center;
    height: 100vh;
    padding: 2rem;
  }

  /* Container */
  .report-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    max-width: 1200px;
    background-color:var(--color-bg-form);
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
    overflow: hidden;
  }

  /* Left Image */
  .report-image {
    flex: 1 1 45%;
    text-align: center;
    padding: 1rem;
  }

  .report-image img {
    width: 80%;
    height: auto;
    max-height: 400px;
    object-fit: contain;
  }

  /* Right Form */
  .report-form {
    flex: 1 1 45%;
    padding: 2rem;
    box-sizing: border-box;
    background-color:  var(--color-bg-form);
  }

  .report-title {
    font-size: 2rem;
    background: linear-gradient(to right, #41E09B, #4AC2D2, #5979F2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1.5rem;
  }

  /* Input Fields */
  .form-group {
    margin-bottom: 1.2rem;
    display: flex;
    flex-direction: column;
  }

  .form-label {
    margin-bottom: 6px;
    background: linear-gradient(to right, #41E09B, #4AC2D2, #5979F2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 600;
  }

  input[type="text"],
  select,
  input[type="file"] {
    padding: 10px 12px;
    border: none;
    border-radius: 6px;
    outline: none;
    font-size: 1rem;
    background: var(--color-bg-form-input);
    color: var(--color-text-primary);
    transition: background 0.3s ease;
  }

  input[type="text"]:focus,
  select:focus {
    background: var( --color-text-placeholder);
  }

  /* Submit Button */
  .submit-btn {
    background-color: rgba(50, 160, 111, 1);
    color: white;
    border: none;
    padding: 12px 28px;
    border-radius: 6px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  .submit-btn:hover {
    background-color: rgba(40, 140, 95, 1);
  }

  /* Responsive Design */
  /* ✅ Mobile Fix */
  @media (max-width: 900px) {

    html,
    body {
      width: 100%;
      overflow-x: hidden;
      /* Prevent horizontal scrolling */
    }

    .report-section {
      padding: 1rem;
      min-height: auto;
      /* allow content to expand naturally */
    }

    .report-container {
      
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      width: 100%;
      margin: 0;
      border-radius: 0;
      box-shadow: none;
    }

    .report-image {
      background-color:var(--color-bg-form);
      width: 100%;
      padding: 0;
      margin-bottom: 1rem;
    }

    .report-image img {
      width: 100%;
      max-width: 100%;
      height: auto;
    }

    .report-form {
      width: 100%;
      padding: 1rem;
      box-sizing: border-box;
      text-align: left;
    }

    .divrow {
      flex-direction: column;
      gap: 10px;
      width: 100%;
    }

    .form-group {
      width: 100%;
    }

    input[type="text"],
    select,
    input[type="file"],
    input[type="date"] {
      width: 100%;
    }

    .submit-btn {
      width: 100%;
      padding: 14px;
      font-size: 1.1rem;
    }
  }
</style>

</html>