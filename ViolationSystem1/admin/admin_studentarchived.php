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

$strandData = mysqli_query($conn, "SELECT * FROM strandcourse_tbl WHERE shs_college=0 OR shs_college=2");






// Handle AJAX restore/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'], $_POST['glevel'])) {
    $id = intval($_POST['id']);
    $glevel = intval($_POST['glevel']); // determine which table
    $action = $_POST['action'];

    // Determine table based on grade level
    if ($glevel >= 11 && $glevel <= 12) {
        $table = 'shs_tbl';
    } elseif ($glevel >= 1 && $glevel <= 4) {
        $table = 'college_tbl';
    } else {
        echo 'Invalid grade level';
        exit;
    }

    if ($action === 'restore') {
        $stmt = $conn->prepare("UPDATE $table SET Archive = 0 WHERE id = ?");
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM $table WHERE id = ?");
    } else {
        echo 'Unknown action';
        exit;
    }

    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo $action === 'restore' ? 'Student restored successfully!' : 'Student permanently deleted!';
    } else {
        echo 'Error performing action';
    }

    exit;
}


    

// Fetch Senior High + College archived students
$sql = "
SELECT 'shs' AS type, shs_tbl.id, shs_tbl.usn, shs_tbl.fname, shs_tbl.lname, shs_tbl.mname,
       strandcourse_tbl.strandcourse, shs_tbl.section, gender_tbl.gender, shs_tbl.birthdate,
       shs_tbl.address, shs_tbl.glevel, collegedep_tbl.department
FROM shs_tbl
JOIN strandcourse_tbl ON shs_tbl.idstrandcourse = strandcourse_tbl.idstrandcourse
JOIN gender_tbl ON shs_tbl.genid = gender_tbl.genid
JOIN collegedep_tbl ON shs_tbl.iddepartment = collegedep_tbl.iddepartment
WHERE shs_tbl.Archive = 1

UNION ALL

SELECT 'college' AS type, college_tbl.id, college_tbl.usn, college_tbl.fname, college_tbl.lname, college_tbl.mname,
       strandcourse_tbl.strandcourse, college_tbl.section, gender_tbl.gender, college_tbl.birthdate,
       college_tbl.address, college_tbl.glevel, collegedep_tbl.department
FROM college_tbl
JOIN strandcourse_tbl ON college_tbl.idstrandcourse = strandcourse_tbl.idstrandcourse
JOIN gender_tbl ON college_tbl.genid = gender_tbl.genid
JOIN collegedep_tbl ON college_tbl.iddepartment = collegedep_tbl.iddepartment
WHERE college_tbl.Archive = 1
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
            <a href="#" class="menu-link ">
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
            <a href="report_violation.php" class="menu-link">
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



          <li class="menu-item has-dropdown">
            <a href="#" class="menu-link dropdown-toggle ">
              <span class="material-symbols-rounded">group</span>
              <span class="menu-label">Student/teacher</span>
              <span class="material-symbols-rounded arrow">expand_more</span>
            </a>
            <ul class="submenu">
              <li class="menu-link"><a href="admin_seniorhigh.php" class="menu-label"
                  style="color:var(--color-text-primary)">Senior High</a></li>
              <li class="menu-link"><a href="admin_college.php" style="color:var(--color-text-primary)">College</a></li>
              <li class="menu-link"><a href="user_management.php" style="color:var(--color-text-primary)">User Logs</a>
              </li>
            </ul>

          </li>
                     <li class="menu-item">
                        <a href="admin_studentarchived.php" class="menu-link active">
                            <span class="material-symbols-rounded">archive</span>
                            <span class="menu-label">Archived Students</span>
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
  scrollbar-width: thin;
  
  ">
  <?php if (!empty($successMsg)): ?>
                <div class="message success">
                    <?= $successMsg ?>
                </div>
            <?php elseif (!empty($errorMsg)): ?>
                <div class="message error">
                    <?= $errorMsg ?>
                </div>
            <?php endif; ?>
            <h1 class="page-title" style="display:none;">Dashboard College</h1>
            <h1 class="card">Archived Dashboard</h1>
            <br>
            

            <br>
            <div id="tableContainer" style="
            overflow-x: auto;overflow-y: hidden;
    -webkit-overflow-scrolling: touch;  width: 100%;
    border-collapse: collapse;
    white-space: nowrap; /* prevent wrapping */">
                <table id="collegeTable" class="display" style="overflow-x: auto;overflow-y: hidden;
    -webkit-overflow-scrolling: touch;  width: 100%;
    border-collapse: collapse;
    white-space: nowrap; /* prevent wrapping */"> 

                <thead>
                    <tr>
                      <th>id</th>
                        <th>USN</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Middle Name</th>
                        <th>Course</th>
                        <th>Grade Level</th>
                         <th>Section</th>
                        <th>Gender</th>
                        <th style='display:none'>Birthdate</th>
                        <th style='display:none'>Address</th>
                        <th style='display:none'>Department</th>
                        <th>Edit Delete</th>

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
                <td>{$row['id']}</td>
                    <td>{$row['usn']}</td>
                    <td>{$row['fname']}</td>
                    <td>{$row['lname']}</td>
                    <td>{$row['mname']}</td>
                    <td>{$row['strandcourse']}</td>
                     <td>{$row['glevel']}</td>
                    <td style='display:none'>{$row['birthdate']}</td>
                    <td style='display:none'>{$row['address']}</td>
                    <td>{$row['section']}</td>
                    <td>{$row['gender']}</td>
                   
                    
                    <td style='display:none'>{$row['department']}</td>
                    <td>
    <button class='editBtn' data-id='{$row['id']}' data-glevel='{$row['glevel']}'>Restore</button>
    <button class='deleteBtn' data-id='{$row['id']}' data-glevel='{$row['glevel']}'>Permanently Delete</button>
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
            


        </div>
        
    </div>

    <!-- Unified Restore/Delete Modal -->
<div id="actionModal" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
     background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
  
  <div style="background:white; padding:25px; border-radius:10px; width:320px; text-align:center;">
      <h3 id="modalTitle" style="margin-bottom:15px;">Confirm Action</h3>
      <p id="modalMessage">Are you sure?</p>

      <button id="confirmAction" 
              style="background:#79C90B;color:white;padding:8px 16px;border:none;border-radius:6px;cursor:pointer;">
          Confirm
      </button>

      <button id="cancelAction" 
              style="background:#ccc;color:black;padding:8px 16px;border:none;border-radius:6px;cursor:pointer; margin-left:10px;">
          Cancel
      </button>
  </div>
</div>

    <script src="../js/main.js"></script>
    <script>
      document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        selectedId = btn.dataset.id;
        actionType = 'restore';
        selectedGlevel = btn.dataset.glevel; // include glevel
        modalTitle.textContent = 'Confirm Restore';
        modalMessage.textContent = 'Are you sure you want to restore this student?';
        confirmAction.style.background = '#79C90B';
        actionModal.style.display = 'flex';
    });
});

document.querySelectorAll('.deleteBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        selectedId = btn.dataset.id;
        actionType = 'delete';
        selectedGlevel = btn.dataset.glevel; // include glevel
        modalTitle.textContent = 'Confirm Permanent Delete';
        modalMessage.textContent = 'Are you sure you want to permanently delete this student?';
        confirmAction.style.background = '#C91B0B';
        actionModal.style.display = 'flex';
    });
});

confirmAction.addEventListener('click', () => {
    if (!selectedId || !actionType) return;

    const formData = new FormData();
    formData.append('id', selectedId);
    formData.append('action', actionType);
    formData.append('glevel', selectedGlevel); // send glevel to PHP

    fetch('', { method: 'POST', body: formData })
    .then(res => res.text())
    .then(data => {
    /*
        alert(data); */
        location.reload();
    })
    .catch(err => console.error(err));
});

 $(document).ready(function () {
            $('#collegeTable').DataTable({
                "pageLength": 10,      // Number of rows per page
                "lengthMenu": [5, 10, 20, 50],  // Page size options
                "ordering": true,       // Enable column sorting
                "order": [[1, "asc"]],  // Default sort by First Name
                "columnDefs": [
                    { "orderable": false, "targets": [0, 9] } // Disable sorting for USN & Department if desired
                ]
            });
        });





 const profilePic = document.getElementById('profilePic');
        const dropdownMenu = document.getElementById('dropdownMenu');

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

        document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('click', e => {
                e.preventDefault();
                const parent = toggle.closest('.menu-item');
                parent.classList.toggle('active');
            });
        });
</script><!-- Custom Delete Confirmation Modal -->


</body>

</html>
<style>
  .editBtn{
background-color:#79C90B;
color:white;
padding:5px;
border-radius:10px;
border:none;
  }
  .deleteBtn{
background-color:#C91B0B; color:white;
padding:5px;
border-radius:10px;
border:none;
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


    #collegeTable {
        width: 100%;
        border-collapse: collapse;
    }

    #collegeTable th,
    #collegeTable td {
        padding: 8px;
        border: 1px solid #dddddd;
    }

    #collegeTable tr:nth-child(even) {
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