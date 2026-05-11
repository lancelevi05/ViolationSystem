<?php
session_start();
include("../config/db_connect.php");

// Ensure only admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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



// Fetch all users with their online status
$query = "SELECT 
        u.id,
        u.fname,
        u.lname,
        u.role,
        gender_tbl.gender,
        u.profile,
        u.last_activity,

        CASE 
            WHEN u.last_activity >= NOW() - INTERVAL 5 MINUTE THEN 1 
            ELSE 0 
        END AS is_online,

        -- COMBINE ALL ADVISORIES AS ONE STRING
        GROUP_CONCAT(
            CONCAT(sc.strandcourse, ' - ', a.glevel, ' - ', a.section)
            SEPARATOR '<br>'
        ) AS advisory

    FROM users u

    LEFT JOIN gender_tbl ON gender_tbl.genid = u.genid
    LEFT JOIN advisory_tbl a ON a.adviser_id = u.id
    LEFT JOIN strandcourse_tbl sc ON sc.idstrandcourse = a.idstrandcourse

    
    WHERE u.role = 'teacher' OR u.role = 'Guidance' OR u.role = 'admin'
    GROUP BY u.id
    ORDER BY u.last_activity DESC
";
$result = mysqli_query($conn, $query);

$strandQuery = mysqli_query($conn, "SELECT * FROM strandcourse_tbl ORDER BY strandcourse ASC");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $adviser_id = $_POST['adviser_id'];

    // Delete old advisories of this adviser
    mysqli_query($conn, "DELETE FROM advisory_tbl WHERE adviser_id = $adviser_id");

    // Fetch all strands
    $strands = mysqli_query($conn, "SELECT idstrandcourse FROM strandcourse_tbl");

    while ($s = mysqli_fetch_assoc($strands)) {
        $sid = $s['idstrandcourse'];

        if (!empty($_POST["glevel_$sid"]) && !empty($_POST["section_$sid"])) {

            foreach ($_POST["glevel_$sid"] as $g) {
                foreach ($_POST["section_$sid"] as $sec) {

                    // ----------------------------------
                    // CHECK FOR DUPLICATE ADVISORY
                    // ----------------------------------
                    $dupCheck = mysqli_query(
                        $conn,
                        "SELECT a.adviser_id, u.fname, u.lname
                         FROM advisory_tbl a
                         INNER JOIN users u ON u.id = a.adviser_id
                         WHERE a.idstrandcourse = '$sid'
                           AND a.glevel = '$g'
                           AND a.section = '$sec'
                           AND a.adviser_id <> '$adviser_id'
                         LIMIT 1"
                    );

                    if (mysqli_num_rows($dupCheck) > 0) {

                        $dup = mysqli_fetch_assoc($dupCheck);
                        $existingAdviser = $dup['fname'] . " " . $dup['lname'];

                        // Get actual strandcourse name
                        $strandRes = mysqli_query(
                            $conn,
                            "SELECT strandcourse
                             FROM strandcourse_tbl
                             WHERE idstrandcourse = '$sid'
                             LIMIT 1"
                        );
                        $strandRow = mysqli_fetch_assoc($strandRes);
                        $strandName = $strandRow['strandcourse'];

                        // Set error message
                        $_SESSION['errorMsg'] = "
                            <strong>Duplicate Advisory Found!</strong><br>

                            <strong>Strandcourse:</strong> $strandName<br>
                            <strong>Grade Level:</strong> $g<br>
                            <strong>Section:</strong> $sec<br>

                            <strong>Already assigned to:</strong><br>
                            $existingAdviser
                        ";

                        header("Location: user_management.php");
                        exit();
                    }

                    // ----------------------------------
                    // INSERT IF NO DUPLICATE
                    // ----------------------------------
                    mysqli_query(
                        $conn,
                        "INSERT INTO advisory_tbl (adviser_id, idstrandcourse, glevel, section)
                         VALUES ($adviser_id, $sid, '$g', '$sec')"
                    );
                }
            }
        }
    }

    $_SESSION['successMsg'] = "Advisory managed successfully!";
    header("Location: user_management.php");
    exit();
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


    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- jQuery (already included) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
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
                    <a href="#">My Profile</a>
                    <a href="#">Settings</a>
                    <a href="../logout.php">Logout</a>
                </div>

            </div>

            <div class="sidebar-content">
                <!-- Search Form -->
                <form action="" class="search-form">
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
          
          <li class="menu-item">
            <a href="admin_violation.php" class="menu-link ">
              <span class="material-symbols-rounded">warning</span>
              <span class="menu-label">Violation Student List</span>
            </a>
          </li>


          <li class="menu-item has-dropdown">
            <a href="#" class="menu-link dropdown-toggle active">
              <span class="material-symbols-rounded">group</span>
              <span class="menu-label">Student/teacher</span>
              <span class="material-symbols-rounded arrow">expand_more</span>
            </a>
            <ul class="submenu">
              <li class="menu-link"><a href="admin_seniorhigh.php" class="menu-label"
                  style="color:var(--color-text-primary)">Senior High</a></li>
              <li class="menu-link"><a href="admin_college.php" style="color:var(--color-text-primary)">College</a></li>
              <li class="menu-link"><a href="user_management.php" style="color:var(--color-text-primary)">Manage Advisory</a>
              </li>
            </ul>

          </li>
          
<li class="menu-item">
            <a href="admin_studentarchived.php" class="menu-link">
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
  
  overflow-x: auto;overflow-y: hidden;
    -webkit-overflow-scrolling: touch;  width: 100%;
    border-collapse: collapse;
    white-space: nowrap; /* prevent wrapping */
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
            <h1 class="page-title" style="display:none">Teacher Management</h1>
            <h1 class="card">Teacher Management</h1>
           
            <br>
            <h2 class="page-title" style="display:none">Teacher</h2>
            <table id="userTable" class="display" >
                <thead>
                    <tr>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Gender</th>
                        <th>Activity</th>
                        <th>Advisory</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($user = mysqli_fetch_assoc($result)) {

                            // Determine activity text
                            if ($user['is_online']) {
                                $activity = "<span class='online-text'>Online now</span>";
                            } else {
                                $last_active = strtotime($user['last_activity'] ?? date('Y-m-d H:i:s'));
                                $now = time();
                                $diff = $now - $last_active;

                                if ($diff < 60) {
                                    $activity = "Offline just now";
                                } elseif ($diff < 3600) {
                                    $activity = "Offline " . floor($diff / 60) . " minutes ago";
                                } elseif ($diff < 86400) {
                                    $activity = "Offline " . floor($diff / 3600) . " hours ago";
                                } else {
                                    $activity = "Offline " . floor($diff / 86400) . " days ago";
                                }
                            }

                            // Output table row
                            echo "
        <tr>
            <td>
                <img src='{$user['profile']}' alt='Profile' class='user-avatar' style='width:100px;'>
                <span class='status-indicator " . ($user['is_online'] ? 'status-online' : 'status-offline') . "'></span>
            </td>
            <td>{$user['fname']} {$user['lname']}</td>
            <td>{$user['role']}</td>
            <td>{$user['gender']}</td>
            <td>{$activity}</td>
            <td >{$user['advisory']}
            <br>
            <button 
    class='editbtn'
    data-id='{$user['id']}'
    data-fname='{$user['fname']}'
    data-lname='{$user['lname']}'
    data-role='{$user['role']}'
    data-gender='{$user['gender']}'
    data-advisory='{$user['advisory']}'
    data-profile='{$user['profile']}'
>Edit</button>
            </td>
        </tr>"; //style='max-width:150px; height:150px; overflow-y:auto; display:block;'
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center;'>No users found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>



            <div class="ps-container" >
    <div class="ps-box">
        <div class="ps-content">

            <!-- LEFT SIDE -->
            <div class="ps-left">
                <span class="ps-title">Profile Settings</span>
                <img src="https://cdn-icons-png.flaticon.com/512/6676/6676023.png" alt="user image" class="ps-img" id="ps-profile-img">
            </div>

            <!-- RIGHT SIDE -->
            <div class="ps-right">
                <form action="user_management.php" method="POST" enctype="multipart/form-data" class="ps-form">

                    <div class="ps-field" style="display:none">
                        <label for="ps-id" class="ps-label">ID</label>
                        <input type="text" id="ps-id" name="adviser_id" class="ps-input" readonly>
                    </div>

                    <div class="ps-field">
                        <label for="ps-fullname" class="ps-label">Full Name</label>
                        <input type="text" id="ps-fullname" class="ps-input" readonly>
                    </div>

                    <div class="ps-field">
                        <label for="ps-role" class="ps-label">Role</label>
                        <input type="text" id="ps-role" class="ps-input" readonly>
                    </div>

                    <div class="ps-field">
                        <label class="ps-label">Advisory Settings</label>
                    </div>

                    <div id="advisory-container">
                        <?php
                        $strands = mysqli_query($conn, "SELECT * FROM strandcourse_tbl ORDER BY strandcourse ASC");
                        while ($row = mysqli_fetch_assoc($strands)) {
                            $sections = "";
                            for ($i = 0; $i < $row['max_section']; $i++) {
                                $letter = chr(65 + $i);
                                $sections .= "<label class='sec-label' style='color:rgb(105, 92, 254);'>
                                                <input type='checkbox' name='section_{$row['idstrandcourse']}[]' value='$letter'> $letter
                                              </label>";
                            }

                            echo "
                            <div class='strand-block'>
                                <label class='strand-title'>
                                    <input type='checkbox' class='strand-checkbox' data-target='strand-{$row['idstrandcourse']}' data-shs='{$row['shs_college']}'>
                                    {$row['strandcourse']}
                                </label>
                                <div class='section-container' id='strand-{$row['idstrandcourse']}' style='display:none;'>
                                    <div class='grid-2cols'>
                                        <div class='grid-group'>
                                            <span>Grade Levels:</span>
                                            <label style='color:rgb(105, 92, 254);' class='glevel-shs'><input type='checkbox' name='glevel_{$row['idstrandcourse']}[]' value='11'> 11</label>
                                            <label style='color:rgb(105, 92, 254);' class='glevel-shs'><input type='checkbox' name='glevel_{$row['idstrandcourse']}[]' value='12'> 12</label>
                                            <label style='color:rgb(105, 92, 254);' class='glevel-col'><input type='checkbox' name='glevel_{$row['idstrandcourse']}[]' value='1'> 1st Year</label>
                                            <label style='color:rgb(105, 92, 254);'  class='glevel-col'><input type='checkbox' name='glevel_{$row['idstrandcourse']}[]' value='2'> 2nd Year</label>
                                            <label style='color:rgb(105, 92, 254);'  class='glevel-col'><input type='checkbox' name='glevel_{$row['idstrandcourse']}[]' value='3'> 3rd Year</label>
                                            <label style='color:rgb(105, 92, 254);' class='glevel-colnonbach'><input type='checkbox' name='glevel_{$row['idstrandcourse']}[]' value='4'> 4th Year</label>
                                        </div>
                                        <div class='grid-group' >
                                            <span>Sections:</span>
                                            $sections
                                        </div>
                                    </div>
                                </div>
                            </div>";
                        }
                        ?>
                    </div>

                    <div class="ps-buttons">
                        <button type="submit" class="ps-btn-save">Save Changes</button>
                        <button type="reset" class="ps-btn-cancel">Cancel</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>





        </div>
    </div>

    <script src="../js/main.js"></script>
    <script>
        
       $(document).ready(function() {

    // Prepare existing advisories for JS duplicate check
    const existingAdvisories = <?php
        $allAdvisories = [];
        $advQuery = mysqli_query($conn, "SELECT a.idstrandcourse, a.glevel, a.section, u.fname, u.lname 
                                        FROM advisory_tbl a 
                                        INNER JOIN users u ON u.id = a.adviser_id");
        while ($row = mysqli_fetch_assoc($advQuery)) {
            $allAdvisories[] = $row;
        }
        echo json_encode($allAdvisories);
    ?>;

    // ---------------------------------------
    // STRAND CHECKBOX TOGGLE AND SHOW/HIDE GRADE LEVELS
    // ---------------------------------------
    $('.strand-checkbox').on('change', function () {
        let target = $(this).data('target');
        let shs = $(this).data('shs'); // 1 = SHS, 2 = College

        // Show/hide section container
        $('#' + target).toggle(this.checked);

        if (this.checked) {
            let container = $('#' + target);

            if (shs == 1) {
                container.find('.glevel-shs').show().find('input').prop('disabled', false);
                container.find('.glevel-col, .glevel-colnonbach').hide().find('input').prop('checked', false).prop('disabled', true);
            } else {
                container.find('.glevel-col').show().find('input').prop('disabled', false);
                container.find('.glevel-shs, .glevel-colnonbach').hide().find('input').prop('checked', false).prop('disabled', true);
            }
        } else {
            // Reset checkboxes if unchecked
            $('#' + target).find('input[type="checkbox"]').prop('checked', false);
        }

        // Check for duplicates
        checkDuplicateAdvisory();
    });

    // ---------------------------------------
    // EDIT BUTTON
    // ---------------------------------------
    $('.editbtn').on('click', function () {
        // Hide table, show form
        $('#userTable_wrapper').hide();
        $('.ps-container').show();

        // Fetch data
        let id = $(this).data('id');
        let fname = $(this).data('fname');
        let lname = $(this).data('lname');
        let role = $(this).data('role');
        let advisory = $(this).data('advisory');
        let profile = $(this).data('profile');

        // Fill form
        $('#ps-id').val(id);
        $('#ps-fullname').val(fname + " " + lname);
        $('#ps-role').val(role);
        $('#ps-profile-img').attr('src', profile);

        // Reset all checkboxes
        $('#advisory-container input[type="checkbox"]').prop('checked', false);

        if(advisory.trim() !== "") {
            let lines = advisory.split('<br>');
            lines.forEach(function(line){
                let parts = line.split(' - ');
                let strand = parts[0].trim();
                let glevel = parts[1].trim();
                let section = parts[2].trim();

                let strandInput = $(".strand-title:contains('" + strand + "')").find('input');
                if(strandInput.length > 0) {
                    let sID = strandInput.data('target').replace('strand-', '');
                    strandInput.prop('checked', true).trigger('change');
                    $("input[name='glevel_" + sID + "[]'][value='" + glevel + "']").prop('checked', true);
                    $("input[name='section_" + sID + "[]'][value='" + section + "']").prop('checked', true);
                }
            });
        }

        checkDuplicateAdvisory(); // initial check
    });

    // ---------------------------------------
    // CANCEL BUTTON
    // ---------------------------------------
    $('.ps-btn-cancel').on('click', function(e) {
        e.preventDefault();
        $('.ps-container').hide();
        $('#userTable_wrapper').show();
    });

    // ---------------------------------------
    // CHECK DUPLICATE FUNCTION
    // ---------------------------------------
    function checkDuplicateAdvisory() {
        let duplicateFound = false;
        let warningMsg = '';

        $('#advisory-container .strand-block').each(function() {
            let strandInput = $(this).find('.strand-checkbox');
            if (!strandInput.is(':checked')) return;

            let sID = strandInput.data('target').replace('strand-', '');
            let selectedGLevels = $(`input[name='glevel_${sID}[]']:checked`).map(function(){ return $(this).val(); }).get();
            let selectedSections = $(`input[name='section_${sID}[]']:checked`).map(function(){ return $(this).val(); }).get();

            selectedGLevels.forEach(function(g) {
                selectedSections.forEach(function(sec) {
                    existingAdvisories.forEach(function(a) {
                        let currentFullname = $('#ps-fullname').val().trim();
                        if (a.fname + ' ' + a.lname === currentFullname) return; // ignore current adviser

                        if (a.idstrandcourse == sID && a.glevel == g && a.section == sec) {
                            duplicateFound = true;
                            warningMsg = `Cannot assign ${a.fname} ${a.lname}'s advisory: Strand ${strandInput.parent().text().trim()}, Grade ${g}, Section ${sec}`;
                        }
                    });
                });
            });
        });

        const saveBtn = $('.ps-btn-save');
        if(duplicateFound) {
            saveBtn.prop('disabled', true).text('Cannot change - Advisory taken');
            if(warningMsg) alert(warningMsg);
        } else {
            saveBtn.prop('disabled', false).text('Save Changes');
        }
    }

    // ---------------------------------------
    // CHECK DUPLICATE ON ANY CHECKBOX CHANGE
    // ---------------------------------------
    $('#advisory-container input[type="checkbox"]').on('change', function() {
        checkDuplicateAdvisory();
    });

    // ---------------------------------------
    // Initialize DataTable
    // ---------------------------------------
    $('#userTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 20, 50],
        "ordering": true,
        "order": [[1, "asc"]],
        "columnDefs": [{ "orderable": false, "targets": [0, 4] }]
    });

    // ---------------------------------------
    // Profile dropdown
    // ---------------------------------------
    const profilePic = document.getElementById('profilePic');
    const dropdownMenu = document.getElementById('dropdownMenu');

    profilePic.addEventListener('click', () => {
        dropdownMenu.classList.toggle('show');
    });
    document.addEventListener('click', (e) => {
        if (!profilePic.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.classList.remove('show');
        }
    });

    // Sidebar dropdown toggle
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', e => {
            e.preventDefault();
            const parent = toggle.closest('.menu-item');
            parent.classList.toggle('active');
        });
    });

});
    </script>
</body>

</html>
<Style>
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



    


    .editbtn {
        background-color: rgb(105, 92, 254);
        padding: 10px;
        border-radius: 20px;
        transition: color 0.3s ease;
    }

   /* ============================
    GENERAL CONTAINER
============================ */
.ps-container {
    display: none; /* Hidden on load */
    height: 100vh;
    width: 100%;
    background-color: #f0f2f5;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.ps-box {
    display: flex;
    flex-direction: row;
    width: 100%;
    height: 100%;
}

.ps-content {
    display: flex;
    flex-direction: row;
    width: 100%;
}

/* LEFT SIDE PROFILE */
.ps-left {
    width: 30%;
    display: flex;
    flex-direction: column;
    align-items: center;
    background-color: var(--color-bg-form);
    padding: 50px 20px;
    border-right: 1px solid rgb(105, 92, 254);
}

.ps-title {
    font-size: 28px;
    font-weight: 700;
    color: rgb(105, 92, 254);
    margin-bottom: 40px;
    text-align: center;
}

.ps-img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 20px;
    border: 2px solid #26c6da;
}

.ps-edit-picture {
    font-size: 16px;
    color: #26c6da;
    text-decoration: none;
    margin-top: 10px;
    font-weight: bold;
}

.ps-edit-picture:hover {
    color: #0097a7;
    transition: 0.3s;
}

/* RIGHT SIDE FORM */
.ps-right {
    width: 70%;
    background-color:var(--color-bg-form);
    padding: 50px 40px;
    overflow-y: auto;
}

.ps-form {
    width: 100%;
}

.ps-field {
    display: flex;
    flex-direction: column;
    margin-bottom: 25px;
}

.ps-label {
    font-size: 16px;
    font-weight: 600;
    color: rgb(105, 92, 254);
    margin-bottom: 8px;
}

.ps-input {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    outline: none;
    width: 80%;
}

.ps-input:focus {
    border-color: #26c6da;
}

/* BUTTONS */
.ps-buttons {
    display: flex;
    justify-content: space-evenly;
    margin-top: 30px;
}

.ps-btn-save,
.ps-btn-cancel {
    width: 40%;
    padding: 12px;
    font-size: 16px;
    font-weight: bold;
    border-radius: 5px;
    cursor: pointer;
    border: none;
}

.ps-btn-save {
    background-color: rgb(105, 92, 254);
    color: #fff;
}

.ps-btn-save:hover {
    background-color: #0097a7;
    transition: 0.3s;
}

.ps-btn-cancel {
    background-color: #ccc;
    color: #333;
}

.ps-btn-cancel:hover {
    background-color: #999;
    transition: 0.3s;
}

/* STRAND / ADVISORY LAYOUT */
.strand-block {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px 20px;
    margin-bottom: 20px;
    background-color: var(--color-bg-secondary);
    transition: box-shadow 0.3s;
}

.strand-block:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.strand-title {
    font-weight: 700;
    font-size: 18px;
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.section-container {
    padding-left: 0;
}

/* TWO-COLUMN GRID FOR LEVELS AND SECTIONS */
.grid-2cols {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    width: 100%;
}

.grid-group {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap: 10px;
}

.grid-group span {
    grid-column: 1 / -1;
    font-weight: bold;
    color: var(--color-text-primary);
    margin-bottom: 8px;
}

.grid-group label {
    display: flex;
    align-items: center;
    padding: 6px;
    border-radius: 5px;
    background-color: #f9f9f9;
    border: 1px solid #ccc;
    cursor: pointer;
    transition: all 0.2s;
}

.grid-group label:hover {
    background-color: #e0f7fa;
    border-color: #26c6da;
}

.grid-group input[type="checkbox"] {
    margin-right: 6px;
}

/* RESPONSIVE */
@media screen and (max-width:1024px) {
    .ps-box {
        flex-direction: column;
        height: auto;
    }

    .ps-left, .ps-right {
        width: 100%;
        padding: 30px 20px;
        
    }
    .ps-right{
        width: 100%;
        padding: 30px 20px;
        max-height: 80vh;  /* Adjust height so it doesn’t overflow screen */
        overflow-y: scroll;

    }

    .grid-2cols {
        grid-template-columns: 1fr;
    }
}

@media screen and (max-width:630px) {
    .grid-group {
        grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
        
    }
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

    /*************************** */

/*
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
    }*/#userTable {
        width: 100%;
        border-collapse: collapse;
    }

    #userTable th,
    #userTable td {
        padding: 8px;
        border: 1px solid #dddddd;
    }

    #userTable tr:nth-child(even) {
        background-color: #470c8a33;
    }
</Style>