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



// === FETCH DATA REQUEST ===
if (isset($_GET['fetchData'])) {
  $month = $_GET['month'] ?? '';
  $year = $_GET['year'] ?? '';

  if ($month && $year) {
    // Filter by month + year
    $sql = "
            SELECT DAY(`date`) AS label, COUNT(*) AS total
            FROM violation
            WHERE MONTH(`date`) = '$month' AND YEAR(`date`) = '$year'
            GROUP BY DAY(`date`)
            ORDER BY label ASC
        ";
  } elseif ($year && !$month) {
    // Filter by year only
    $sql = "
            SELECT MONTH(`date`) AS label, COUNT(*) AS total
            FROM violation
            WHERE YEAR(`date`) = '$year'
            GROUP BY MONTH(`date`)
            ORDER BY label ASC
        ";
  } else {
    echo json_encode(['labels' => [], 'values' => [], 'total' => 0, 'status' => [0, 0, 0], 'message' => 'Please select a filter option.']);
    exit;
  }

  $result = mysqli_query($conn, $sql);
  $labels = [];
  $values = [];

  while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['label'];
    $values[] = $row['total'];
  }

  if (empty($labels)) {
    echo json_encode(['labels' => [], 'values' => [], 'total' => 0, 'status' => [0, 0, 0], 'message' => 'No records found.']);
  } else {
    $total = array_sum($values);






    // ===== PIE CHART FILTER (MATCH DATE FILTER) =====

    // Build date filter condition
    $dateCondition = "";
    if (!empty($month) && !empty($year)) {
      $dateCondition = " AND MONTH(`date`) = '$month' AND YEAR(`date`) = '$year' ";
    } elseif (!empty($year)) {
      $dateCondition = " AND YEAR(`date`) = '$year' ";
    }

    // SHS = glevel 11 - 12
    $shsSql = "
    SELECT COUNT(*) AS total 
    FROM violation
    WHERE glevel BETWEEN 11 AND 12
    $dateCondition
";

    // COLLEGE = glevel 1 - 4
    $collegeSql = "
    SELECT COUNT(*) AS total 
    FROM violation
    WHERE glevel BETWEEN 1 AND 4
    $dateCondition
";

    $shsResult = mysqli_fetch_assoc(mysqli_query($conn, $shsSql))['total'];
    $collegeResult = mysqli_fetch_assoc(mysqli_query($conn, $collegeSql))['total'];

    $statusCounts = [$shsResult, $collegeResult];


    echo json_encode([
      'labels' => $labels,
      'values' => $values,
      'total' => $total,
      'status' => $statusCounts,
      'message' => 'success'
    ]);
  }
  exit;
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
              <li class="menu-link"><a href="admin_seniorhigh.php" class="menu-label"
                  style="color:var(--color-text-primary)">Senior High</a></li>
              <li class="menu-link"><a href="admin_college.php" style="color:var(--color-text-primary)">College</a></li>
              <li class="menu-link"><a href="user_management.php" style="color:var(--color-text-primary)">User Logs</a>
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
    <div class="main-content" style="
  overflow: hidden auto;
  scrollbar-width: thin;">
      <h1 class="page-title" style="display:none">Admin Side</h1>
      <h1 class="card">Analytics</h1>
      <br>


      <br>
      <div class="filter-container" style="margin-bottom: 10px;">
        <label for="filterType"><b>Filter By:</b></label>
        <select id="filterType" class="filter-select">
          <option value="">-- Select Filter Type --</option>
          <option value="month">By Month</option>
          <option value="year">By Year</option>
        </select>

        <select id="monthSelect" class="filter-select" style="display:none;">
          <?php
          $currentMonth = date('m'); // current month
          for ($m = 1; $m <= 12; $m++) {
            $monthValue = str_pad($m, 2, "0", STR_PAD_LEFT);
            $monthName = date('F', mktime(0, 0, 0, $m, 10));
            $selected = $monthValue === $currentMonth ? 'selected' : '';
            echo "<option value='$monthValue' $selected>$monthName</option>";
          }
          ?>
        </select>

        <select id="yearSelect" class="filter-select" style="display:none;">
          <?php
          $currentYear = date('Y'); // current year
          for ($y = 2020; $y <= 2030; $y++) {
            $selected = $y == $currentYear ? 'selected' : '';
            echo "<option value='$y' $selected>$y</option>";
          }
          ?>
        </select>
      </div>

      <div id="message" style="color:orange; font-weight:bold;"></div>

      <canvas id="myChart" style="width:900px;height:300px;background-color:var(--color-bg-form)"></canvas>
      <h4 id="totalCount" style="margin-top:10px;">Total violations: 0</h4>

      <h3>Status Distribution</h3>
      <canvas id="statusChart" style="width:1100px;height:400px;background-color:var(--color-bg-form)"></canvas>

    </div>

  </div>
  <script src="../js/main.js"></script>
  <script>
    window.addEventListener('DOMContentLoaded', () => {
      const today = new Date();
      const currentMonth = String(today.getMonth() + 1).padStart(2, '0');

      // Show month filter by default
      filterType.value = 'month';
      monthSelect.style.display = 'inline-block';
      yearSelect.style.display = 'none';

      fetchData('month', currentMonth);
    });

    // DOM Elements
    const filterType = document.getElementById('filterType');
    const monthSelect = document.getElementById('monthSelect');
    const yearSelect = document.getElementById('yearSelect');
    const msg = document.getElementById('message');
    const totalText = document.getElementById('totalCount');

    // Bar Chart
    const ctx = document.getElementById('myChart').getContext('2d');
    let myChart = new Chart(ctx, {
      type: 'bar',
      data: { labels: [], datasets: [{ label: 'Violations', data: [], backgroundColor: 'rgb(105, 92, 254)' }] },
      options: {
        responsive: true, // disable auto-scaling
        maintainAspectRatio: true, // allow custom height/width
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
      }
    });


    // Pie Chart
    const ctxPie = document.getElementById('statusChart').getContext('2d');

    let statusChart = new Chart(ctxPie, {
      type: 'pie',
      data: {
        labels: ['Senior High School (SHS)', 'College'],
        datasets: [{
          data: [0, 0],
          backgroundColor: ['#FFD54F', '#4CAF50']
        }]
      },
      options: {
        responsive: false,
        maintainAspectRatio: true,
        plugins: { legend: { position: 'bottom' } }
      }
    });

    // Toggle filters

    filterType.addEventListener('change', () => {
      monthSelect.style.display = filterType.value === 'month' ? 'inline-block' : 'none';
      yearSelect.style.display = filterType.value === 'year' ? 'inline-block' : 'none';

      msg.textContent = '';
      totalText.textContent = 'Total violations: 0';
      myChart.data.labels = []; myChart.data.datasets[0].data = [];
      myChart.update();

      statusChart.data.datasets[0].data = data.status;
      statusChart.update();
    });

    // Fetch Data
    async function fetchData(filter, value) {
      msg.textContent = "Loading...";
      let query = '';
      if (filter === 'month') {
        const year = new Date().getFullYear();
        query = `month=${value}&year=${year}`;
      } else if (filter === 'year') {
        query = `year=${value}`;
      }

      const response = await fetch(`admin_summary.php?fetchData=1&${query}`);
      const data = await response.json();

      if (data.message && data.message !== 'success') {
        msg.textContent = data.message;
        myChart.data.labels = [];
        myChart.data.datasets[0].data = [];
        myChart.update();

        statusChart.data.datasets[0].data = data.status;
        statusChart.update();
        totalText.textContent = "Total violations: 0";
      } else {
        msg.textContent = "";

        // Format labels
        let formattedLabels = data.labels.map(label => {
          if (filterType.value === 'month' && monthSelect.style.display === 'inline-block') {
            // Display day of the month
            return `Day ${label}`;
          } else if (filterType.value === 'year' && yearSelect.style.display === 'inline-block') {
            // Display month name
            const monthIndex = parseInt(label) - 1; // JS months 0-11
            return ["January", "Febuary", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"][monthIndex] || label;
          } else {
            return label;
          }
        });

        myChart.data.labels = formattedLabels;
        myChart.data.datasets[0].data = data.values;
        myChart.update();

        statusChart.data.datasets[0].data = data.status;
        statusChart.update();

        totalText.textContent = "Total violations: " + (data.total ?? data.values.reduce((a, b) => a + parseInt(b), 0));
      }
    }

    // Event listeners
    monthSelect.addEventListener('change', () => { if (monthSelect.value) fetchData('month', monthSelect.value); });
    yearSelect.addEventListener('change', () => { if (yearSelect.value) fetchData('year', yearSelect.value); });

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
  </script>
</body>

</html>
<style>
  #statusChart {
    width: 900px;
    /* desktop width */
    height: 400px;
    /* desired desktop height */
    max-width: 100%;
    /* scale down on smaller screens */
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

  .filter-container {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .filter-select {
    padding: 5px 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
  }
</style>