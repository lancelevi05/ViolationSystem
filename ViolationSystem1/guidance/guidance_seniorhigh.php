<?php
session_start();
include("../config/db_connect.php");

// Session check
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}


$successMsg = $_SESSION['successMsg'] ?? '';
$errorMsg = $_SESSION['errorMsg'] ?? '';

// Clear messages after displaying
unset($_SESSION['successMsg']);
unset($_SESSION['errorMsg']);

$name = $_SESSION['user_name'];
$profile = $_SESSION['profile'];

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

// SQL query to fetch student data
$sql = "
SELECT 
    shs_tbl.usn, 
    shs_tbl.id, 
    shs_tbl.lname, 
    shs_tbl.fname, 
    shs_tbl.mname, 
    shs_tbl.section, 
    shs_tbl.idstrandcourse,
    strandcourse_tbl.strandcourse, 
    gender_tbl.gender,
    shs_tbl.birthdate, 
    shs_tbl.address, 
    shs_tbl.glevel, 
    shsdep_tbl.department,
    shs_tbl.vio_record
FROM shs_tbl
JOIN strandcourse_tbl ON shs_tbl.idstrandcourse = strandcourse_tbl.idstrandcourse
JOIN gender_tbl ON shs_tbl.genid = gender_tbl.genid
JOIN shsdep_tbl ON shs_tbl.iddepartment = shsdep_tbl.iddepartment
";

$result = mysqli_query($conn, $sql);
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

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<!-- jQuery (already included) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<body style="">

    <!-- Navbar -->
    <nav class="site-nav" style="">
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
                        <a href="guidance_dashboard.php" class="menu-link">
                            <span class="material-symbols-rounded">dashboard</span>
                            <span class="menu-label">Dashboard</span>
                        </a>
                    </li>
                     <hr>
          <li class="menu-item">
            <a href="guidance_myreport.php" class="menu-link ">
              <span class="material-symbols-rounded">report</span>
              <span class="menu-label">My reports</span>
            </a>
          </li>
          <li class="menu-item">
            <a href="report_violation.php" class="menu-link">
              <span class="material-symbols-rounded">forms_add_on</span>
              <span class="menu-label">File Report</span>

            </a>
          </li>

          <li class="menu-item">
            <a href="guidance_advisorydashboard.php" class="menu-link">
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
                        <a href="guidance_summary.php" class="menu-link">
                            <span class="material-symbols-rounded">insert_chart</span>
                            <span class="menu-label">Analytics</span>
                        </a>
                    </li>
                   
                     <li class="menu-item">
            <a href="guidance_violation.php" class="menu-link ">
              <span class="material-symbols-rounded">warning</span>
              <span class="menu-label">Violation Student</span>
            </a>
          </li>

                    <li class="menu-item has-dropdown ">
                        <a href="#" class="menu-link dropdown-toggle active">
                            <span class="material-symbols-rounded">group</span>
                            <span class="menu-label">Users</span>
                            <span class="material-symbols-rounded arrow">expand_more</span>
                        </a>
                        <ul class="submenu">
                            <li class="menu-link"><a href="guidance_seniorhigh.php" class="menu-label" style="color:var(--color-text-primary)">Senior High</a></li>
                            <li class="menu-link"><a href="guidance_college.php" style="color:var(--color-text-primary)">College</a></li>
                           
                        </ul>

                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <span class="material-symbols-rounded">settings</span>
                            <span class="menu-label">Settings</span>
                        </a>
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
            <h1 class="page-title" style="display:none;">Dashboard shs</h1>
            <h1 class="card">Dashboard College</h1>
            <br>
           

            <br>

            <div id="tableContainer" style="overflow-x: auto;overflow-y: hidden;
    -webkit-overflow-scrolling: touch;  width: 100%;
    border-collapse: collapse;
    white-space: nowrap; /* prevent wrapping */">
    <table id="shsTable" class="display" style=" ">

                <thead>
                    <tr>
                        <th style="display:none">id</th>
                        <th>USN</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Middle Name</th>
                        <th>Course</th>
                        <th>Grade Level</th>
                        <th>Section</th>
                        <th>Gender</th>
                        <th>Birthdate</th>
                        <th>Address</th>                       
                        <th style="display:none">Department</th>
                        <th>Total Violation</th>      
                        <th></th>    

                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Combine name parts
                            /*
                            $fullname = $row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname']; */
                            echo "
                
                <tr>
                <td style='display:none'>{$row['id']}</td>
                    <td>{$row['usn']}</td>
                    <td>{$row['fname']}</td>
                    <td>{$row['lname']}</td>
                    <td>{$row['mname']}</td>
                    <td>{$row['strandcourse']}</td>
                    <td>{$row['glevel']}</td>
                    <td>{$row['section']}</td>

                    <td>{$row['gender']}</td>
                    <td>{$row['birthdate']}</td>
                    <td>{$row['address']}</td>
                    
                    <td style='display:none'>{$row['department']}</td>
                    <td>{$row['vio_record']}</td>
                     <th>
        <button  class='viewBtn' data-id='{$row['id']}'>view</button>
        </td>
                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10' style='text-align:center;'>No data found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        

         <section class="report-section" id="formContainer" style="display:none;">
        <div class="report-container">

          <!-- Left Image -->
          <div class="report-image">
            <img src="../img/guidance.png" alt="Report Image" style="width:500px">
          </div>

          <!-- Right Form -->
          <div class="report-form">
            <h2 class="report-title">view SHS Student</h2>

            <form action="guidance_seniorhigh.php" method="POST" enctype="multipart/form-data">
              <!-- Hidden student ID for update -->
              <input readonly style="display:none" type="text" name="id" id="id">
              <!-- USN Input -->
              <div class="form-group">
                <label for="usn" class="form-label">USN</label>
                <input readonly type="text" id="usn" name="usn" placeholder="Enter student's USN" maxlength="11"
                  inputmode="numeric" pattern="\d{11}" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                  required>
              </div>

              <div class="divrow">
                <!-- Last Name -->
                <div class="form-group">
                  <label for="lname" class="form-label">Last Name</label>
                  <input readonly type="text" id="lname" name="lname" placeholder="Enter valid last name" required>
                </div>

                <!-- First Name -->
                <div class="form-group">
                  <label for="fname" class="form-label">First Name</label>
                  <input readonly type="text" id="fname" name="fname" placeholder="Enter valid first name" required>
                </div>

                <!-- Middle Name -->
                <div class="form-group">
                  <label for="mname" class="form-label">Middle Name</label>
                  <input readonly type="text" id="mname" name="mname" placeholder="Enter valid middle name">
                </div>
              </div>


              <div class="divrow">
                <!-- Course -->
                <div class="form-group">
                  <label for="course" class="form-label">Course</label>
         
                 <input type="text" id="course" name="course" readonly>
                </div>

                <!-- Year Level -->
                <div class="form-group">
                  <label for="glevel" class="form-label">Year Level</label>
                 
                    
                    <input type="text" id="glevel" name="glevel" readonly>
               
                </div>

                <!-- Section -->
                <div class="form-group">
                  <label for="section" class="form-label">Section</label>
                 
                  <input type="text" id="section" name="section" readonly>
                </div>

              </div>


 <!-- totalvio -->
                <div class="form-group">
                  <label for="vio_record" class="form-label">Total violation</label>
                  <input readonly type="text" id="vio_record" name="vio_record" placeholder="Enter valid middle name">
                </div>


              <!-- Gender -->
              <div class="form-group">
                <label for="gender" class="form-label">Gender</label>
               
                <input readonly type="text" id="gender" name="gender" required>
              </div>



              <!-- Birthdate -->
              <div class="form-group">
                <label for="bdate" class="form-label">Birthdate</label>
                <input readonly type="date" id="bdate" name="bdate" required>
              </div>

              <!-- Full Address (Readonly) -->
              <div class="form-group">
                <label for="address" class="form-label">Address</label>
                <input readonly type="text" id="address" name="address" placeholder="Full Address" readonly required>
              </div>

              <!-- Submit Button -->
              <div class="form-action">
                <button type="submit" class="submit-btn">Done View</button>
                
              </div>

            </form>
          </div>
        </div>
      </section>


        </div>
    </div>
    <script src="../js/main.js"></script>
    <script>
        $(document).ready(function () {
            $('#shsTable').DataTable({
                "pageLength": 10,      // Number of rows per page
                "lengthMenu": [5, 10, 20, 50],  // Page size options
                "ordering": true,       // Enable column sorting
                "order": [[1, "asc"]],  // Default sort by First Name
                "columnDefs": [
                    { "orderable": false, "targets": [0, 10] } // Disable sorting for USN & Department if desired
                ]
            });
        });




      /* ================= EDIT BUTTON PREFILL ================= */
      document.querySelectorAll('.viewBtn').forEach(button => {
        button.addEventListener('click', async function () {
          const studentId = this.dataset.id;

          document.getElementById('tableContainer').style.display = 'none';
          document.getElementById('formContainer').style.display = 'block';

          const res = await fetch('fetch_student.php?id=' + studentId);
          const data = await res.json();
          document.getElementById('id').value = data.id;
          document.getElementById('usn').value = data.usn;
          document.getElementById('fname').value = data.fname;
          document.getElementById('lname').value = data.lname;
          document.getElementById('mname').value = data.mname;
          
          document.getElementById('glevel').value = data.glevel;
          document.getElementById('course').value = data.strandcourse;
        
          document.getElementById('section').value = data.section; // section remains readonly input
          document.getElementById('vio_record').value = data.vio_record;
          document.getElementById('gender').value = data.gender;
          document.getElementById('bdate').value = data.birthdate;
           document.getElementById('address').value = data.address;
          document.getElementById('address').value = data.address;
          // Course & Section
         
          
       

          // ===== PREFILL FULL ADDRESS =====
         
        });
      });





        document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('click', e => {
                e.preventDefault();
                const parent = toggle.closest('.menu-item');
                parent.classList.toggle('active');
            });
        });

        // Toggle dropdown on profile click
        profilePic.addEventListener('click', () => {
            dropdownMenu.classList.toggle('show');
        });

        // Optional: Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!profilePic.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    </script>
</body>

</html>
<style>
 .viewBtn {
    background-color: #0b28c9ff;
    color: white;
    padding: 5px;
    border-radius: 10px;
    border: none;
  }

  .deleteBtn {
    background-color: #C91B0B;
    color: white;
    padding: 5px;
    border-radius: 10px;
    border: none;
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
    background: var(--color-bg-form);
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
  input[type="date"],
  select,
  input[type="file"] {
    padding: 10px 12px;
    border: none;
    border-radius: 6px;
    outline: none;
    font-size: 1rem;
    background: var(--color-bg-form-input);
    color: white;
    transition: background 0.3s ease;
  }

  input[type="text"]:focus,
  select:focus {
    background: #444;
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


    #shsTable {
        width: 100%;
        border-collapse: collapse;
        
    }

    #shsTable th,
    #shsTable td {
        padding: 8px;
        border: 1px solid #dddddd;
        text-align: center;
    }

    #shsTable tr:nth-child(even) {
        background-color: #470c8a33;
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



    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td,
    th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #470c8a33;
    }
</style>