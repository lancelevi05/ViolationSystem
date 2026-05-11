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
unset($_SESSION['successMsg']);
unset($_SESSION['errorMsg']);

// Update last activity
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


// Fetch violations (no search & sort yet — JS will handle it)
$sql = "
SELECT 
    violation.vi_id,
    violation.usn,
    CONCAT(violation.lname, ', ', violation.fname, ', ', violation.mname) AS person,
    violation.lname,
    violation.fname,
    violation.mname,
    violation.location,
    violation.glevel,
    violation.section,
    violation.typeviolation,
    violation.description,
    violation.status_id,
    violation.evidence,
    violation.reportedBy,
    violation.date,
    violation.date_resolved,
    violationstatus.vio_stats,
    strandcourse_tbl.strandcourse,
    CONCAT(users.fname, ' ', users.lname) AS adviser_name

FROM violation
LEFT JOIN strandcourse_tbl 
    ON violation.idstrandcourse = strandcourse_tbl.idstrandcourse
LEFT JOIN violationstatus 
    ON violation.status_id = violationstatus.status_id
LEFT JOIN advisory_tbl 
    ON violation.idstrandcourse = advisory_tbl.idstrandcourse
    AND violation.glevel = advisory_tbl.glevel
    AND violation.section = advisory_tbl.section
LEFT JOIN users 
    ON advisory_tbl.adviser_id = users.id
ORDER BY date DESC
";

$result = mysqli_query($conn, $sql);

// ============================
// INVESTIGATION FIRST CODE
// ============================
if (isset($_GET['investigate'])) {
    $id = intval($_GET['investigate']);

    mysqli_query($conn, "UPDATE violation SET status_id = 3 WHERE vi_id = $id");

    $_SESSION['successMsg'] = "Case marked for investigation first.";
    header("Location: guidance_violation.php");
    exit();
}



// ============================
// DISMISS CASE CODE (unchanged)
// ============================
if (isset($_GET['id'])) {
  $id = intval($_GET['id']);

  $getStudent = mysqli_query($conn, "SELECT * FROM violation WHERE vi_id = $id");

  if ($getStudent && mysqli_num_rows($getStudent) > 0) {
    $violation = mysqli_fetch_assoc($getStudent);
    $lname = mysqli_real_escape_string($conn, $violation['lname']);
    $fname = mysqli_real_escape_string($conn, $violation['fname']);
    $mname = mysqli_real_escape_string($conn, $violation['mname']);
    $location = mysqli_real_escape_string($conn, $violation['location']);
    $typeviolation = mysqli_real_escape_string($conn, $violation['typeviolation']);
    $description = mysqli_real_escape_string($conn, $violation['description']);
    $evidence = mysqli_real_escape_string($conn, $violation['evidence']);
    $reportedBy = mysqli_real_escape_string($conn, $violation['reportedBy']);
    $date = $violation['date'];
    $status_id = $violation['status_id'];
    $idstrandcourse = $violation['idstrandcourse'];
    $glevel = mysqli_real_escape_string($conn, $violation['glevel']);
    $section = mysqli_real_escape_string($conn, $violation['section']);
    $usn = mysqli_real_escape_string($conn, $violation['usn']);

    // archive?
    mysqli_query($conn, "UPDATE violation SET status_id = 2, date_resolved = NOW() WHERE vi_id = $id");


    /*
    $insert = "
            INSERT INTO past_violation 
            (usn, lname, fname, mname, location, typeviolation, description, evidence, reportedBy, date, status_id, idstrandcourse, glevel, section, date_resolved)
            VALUES
            ('$usn','$lname', '$fname', '$mname', '$location', '$typeviolation', '$description', '$evidence', '$reportedBy', '$date', $status_id, $idstrandcourse, '$glevel', '$section' , NOW())
        ";
    mysqli_query($conn, $insert);
*/
    $_SESSION['successMsg'] = "Student's violation dismissed, updated, and archived";
  } else {
    $_SESSION['errorMsg'] = "Violation not found!";
  }

  header("Location: guidance_violation.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Guidance Page</title>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
  <link rel="stylesheet" href="../assets/style.css">
  <link rel="icon" type="image/x-icon" href="../img/aclc-Photoroom.png">

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
          <span id="userName"><?php echo htmlspecialchars($name); ?></span>
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
        <form action="#" class="search-form">
          <span class="material-symbols-rounded">search</span>
          <input type="search" placeholder="Search..." required />
        </form>
        <!-- Sidebar Menu -->
        <ul class="menu-list">
          <li class="menu-item">
            <a href="guidance_dashboard.php" class="menu-link ">
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
            <a href="guidance_violation.php" class="menu-link active">
              <span class="material-symbols-rounded">warning</span>
              <span class="menu-label">Violation Student</span>
            </a>
          </li>


          <li class="menu-item has-dropdown">
            <a href="#" class="menu-link dropdown-toggle">
              <span class="material-symbols-rounded">group</span>
              <span class="menu-label">List of Students</span>
              <span class="material-symbols-rounded arrow">expand_more</span>
            </a>
            <ul class="submenu">
              <li class="menu-link"><a href="guidance_seniorhigh.php" class="menu-label"
                  style="color:var(--color-text-primary)">Senior High</a></li>
              <li class="menu-link"><a href="guidance_college.php" style="color:var(--color-text-primary)">College</a>
              </li>

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


    <div class="main-content">
      <?php if (!empty($successMsg)): ?>
        <div class="message success">
          <?= $successMsg ?>
        </div>
      <?php elseif (!empty($errorMsg)): ?>
        <div class="message error">
          <?= $errorMsg ?>
        </div>
      <?php endif; ?>
      <h1 class="page-title" style="display:none">Dashboard Overview</h1>
      <h1 class="card">Violation Student</h1>
      <br>

      <!-- ==========================================================
           🔎 SEARCH + FILTER BAR  (ADDED)
      =========================================================== -->
      <div style="display:flex; justify-content:space-between; margin-bottom:15px; width:95%;">

        <!-- Search -->
        <input id="searchInput" type="text" placeholder="Search name..."
          style="padding:10px;width:60%;border-radius:6px;border:1px solid #999;">

        <!-- Sort Filter -->
        <select id="statusFilter" style="padding:10px; width:35%; border-radius:6px; border:1px solid #999;">
          <option value="all">All Status</option>
          <option value="1">Ongoing</option>
          <option value="2">Resolved</option>
          <option value="3">Pending</option>
        </select>

      </div>

       <!-- ==========================================================
           REPORT GRID
      =========================================================== -->
      <div class="reports-section">
        <div class="report-grid" id="reportGrid">

          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="report-card" 
                 data-status="<?= $row['status_id'] ?>"
                 data-name="<?= strtolower($row['lname'] . ' ' . $row['fname'] . ' ' . $row['mname']) ?>">

              <img src="<?= $row['evidence'] ?>" class="report-img">

              <div class="report-info">
                <h2 class="report-name"><?= $row['person'] ?></h2>
                <p style="display:none"><strong>Location:</strong> <?= $row['usn'] ?></p>
                <p><strong>Location:</strong> <?= $row['location'] ?></p>
                <p><strong>Description:</strong> <?= $row['description'] ?></p>
                <p><strong>Type:</strong> <?= $row['typeviolation'] ?></p>
                <p><strong>Status:</strong>
                  <span class="status-<?= $row['status_id'] ?>"><?= $row['vio_stats'] ?></span>
                </p>
                <p><strong>STRAND/COURSE:</strong> <?= $row['strandcourse'] . '-' . $row['glevel'] . $row['section'] ?></p>
                
                <p><strong>Reported By:</strong> <?= $row['reportedBy'] ?></p>
                <p><strong>Adviser:</strong> <?= $row['adviser_name'] ?: 'No Adviser Assigned' ?></p>
                <p class="report-date"><strong>Date submitted:</strong> <?= $row['date'] ?></p>

                <?php if ($row['status_id'] == 3 || $row['status_id'] == 1): ?>
                  <button class="submit-btn" data-id="<?= $row['vi_id'] ?>" style="
                    background-color:#7AEE0F; padding:5px 15px; border-radius:50px; cursor:pointer;">Resolve Case</button>
                <?php else: ?>
                  <p class="report-date"><strong>Date Resolved:</strong> <?= $row['date_resolved'] ?></p>
                  <span style="color:gray;font-weight:bold;">Case Dismissed</span>
                <?php endif; ?>

              </div>
            </div>
          <?php endwhile; ?>

        </div>
      </div>

    </div>
  </div>

      <!---/////REPORT CARD-->


    </div>
  </div>
  

  <div id="confirmModal" class="modal">
  <div class="modal-content">
    <h2 class="modal-title">Resolve Violation Case?</h2>
    <p class="modal-text">Choose an action for this student's violation report.</p>

    <div class="modal-btn-group">
      <button id="yesBtn" class="btn resolve">Resolve Case</button>
      <button id="investigateBtn" class="btn investigate">Investigation First</button>
      <button id="noBtn" class="btn cancel">Cancel</button>
    </div>
  </div>
</div>
  <script src="../js/main.js"></script>
  <script>
    // ==========================================================
    // 🔎 LIVE SEARCH + FILTER (JS ONLY)
    // ==========================================================

    const searchInput = document.getElementById("searchInput");
    const filterSelect = document.getElementById("statusFilter");
    const cards = document.querySelectorAll(".report-card");

    function applyFilters() {
      let search = searchInput.value.toLowerCase();
      let status = filterSelect.value;

      cards.forEach(card => {
        let name = card.dataset.name;
        let cardStatus = card.dataset.status;

        let matchesSearch = name.includes(search);
        let matchesStatus = (status === "all") || (status === cardStatus);

        card.style.display = (matchesSearch && matchesStatus) ? "block" : "none";
      });
    }

    searchInput.addEventListener("input", applyFilters);
    filterSelect.addEventListener("change", applyFilters);


    


    document.querySelectorAll('.submit-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        selectedViolationId = btn.dataset.id;
        document.getElementById('confirmModal').style.display = 'flex';
      });
    });

    document.getElementById('noBtn').onclick = () => {
      document.getElementById('confirmModal').style.display = 'none';
    };

    document.getElementById('yesBtn').onclick = () => {
      window.location.href = "guidance_violation.php?id=" + selectedViolationId;
    };

    document.getElementById('investigateBtn').onclick = () => {
  window.location.href = "guidance_violation.php?investigate=" + selectedViolationId;
};



    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
      toggle.addEventListener('click', e => {
        e.preventDefault();
        const parent = toggle.closest('.menu-item');
        parent.classList.toggle('active');
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
  </script>
</body>
<style>
/* DARKEN BACKGROUND */
.modal {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  justify-content: center;
  align-items: center;
  animation: fadeIn 0.25s ease;
  z-index: 9999;
}

/* MODAL BOX */
.modal-content {
  background: var(--color-bg-primary);
  padding: 25px 30px;
  border-radius: 14px;
  width: 330px;
  text-align: center;
  animation: popIn 0.25s ease;
  box-shadow: 0 8px 20px rgba(0,0,0,0.35);
}

/* TITLE */
.modal-title {
  font-size: 1.25rem;
  font-weight: bold;
  margin-bottom: 10px;
  color: var(--color-text-primary);
}

/* SUB TEXT */
.modal-text {
  font-size: 0.95rem;
  margin-bottom: 25px;
  color: var(--color-text-primary);
  opacity: 0.85;
}

/* BUTTON GROUP */
.modal-btn-group {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* BUTTONS */
.btn {
  padding: 10px 14px;
  border: none;
  border-radius: 8px;
  font-size: 0.95rem;
  cursor: pointer;
  transition: 0.25s ease;
  font-weight: bold;
}

.btn.resolve {
  background: #2ecc71;
  color: white;
}
.btn.resolve:hover {
  background: #28b765;
}

.btn.investigate {
  background: #0d7ad4ff;
  color: #000;
}
.btn.investigate:hover {
  background: #0d6ad4ff;
}

.btn.cancel {
  background: #bdc3c7;
  color: black;
}
.btn.cancel:hover {
  background: #a6b0b4;
}

/* ANIMATIONS */
@keyframes fadeIn {
  from { opacity: 0; }
  to   { opacity: 1; }
}

@keyframes popIn {
  from { transform: scale(0.9); opacity: 0; }
  to   { transform: scale(1); opacity: 1; }
}


  #userName {
    color: var(--color-text-primary);
  }

  .status-1 {
    color: #ff9100ff;
    font-weight: bold;
  }

  /* Yellow */
  .status-2 {
    color: #2ecc71;
    font-weight: bold;
  }

  /* Green */
  .status-3 {
    color: #3498db;
    font-weight: bold;
  }

  /* Blue */


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



  .reports-section {
    margin: 40px auto;
    padding: 20px;
    width: 95%;
    max-width: 1400px;
  }

  .reports-section h1 {
    text-align: center;
    color: white;
    margin-bottom: 25px;
    font-weight: 700;
    letter-spacing: 1px;
  }

  /* === Responsive Grid Layout === */
  .report-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
    justify-items: center;
  }

  /* Force 5 cards per row on large screens */
  @media (min-width: 1400px) {
    .report-grid {
      grid-template-columns: repeat(5, 1fr);
    }
  }

  /* Adjust for medium screens */
  @media (max-width: 1200px) {
    .report-grid {
      grid-template-columns: repeat(4, 1fr);
    }
  }

  @media (max-width: 992px) {
    .report-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media (max-width: 768px) {
    .report-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 500px) {
    .report-grid {
      grid-template-columns: 1fr;
    }
  }

  /* === Card Styling === */
  .report-card {
    background-color: var(--color-bg-form);
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    overflow: hidden;
    width: 100%;
    max-width: 260px;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .report-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 18px rgba(255, 255, 255, 0.12);
  }

  /* === Image === */
  .report-img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-bottom: 3px solid rgb(105, 92, 254);
  }

  /* === Text Info === */
  .report-info {
    padding: 15px;
    color: #var(--color-text-primary);
    font-size: 0.9rem;
  }

  .report-name {
    background: linear-gradient(to right, #41E09B, #4AC2D2, #5979F2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: 8px;
  }

  .report-info p {
    margin: 4px 0;
    line-height: 1.3;
  }

  .report-date {
    margin-top: 10px;
    font-size: 0.8rem;
    color: #aaa;
  }

  .no-reports {
    text-align: center;
    color: #ccc;
    margin-top: 20px;
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
</style>

</html>