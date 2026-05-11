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
            <a href="#" class="menu-link ">
              <span class="material-symbols-rounded">dashboard</span>
              <span class="menu-label">Dashboard</span>
            </a>
          </li>
          <li class="menu-item">
            <a href="teacher_notification.php" class="menu-link active">
              <span class="material-symbols-rounded">notifications</span>
              <span class="menu-label">Notification</span>
            </a>
          </li>

          <li class="menu-item">
            <a href="teacher_myreport.php" class="menu-link ">
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
            <a href="teacher_advisorydashboard.php" class="menu-link">
              <span class="material-symbols-rounded">group</span>
              <span class="menu-label">Advisory</span>

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

    <div class="main-content">
   

      <h1 class="page-title" style="display:none">Dashboard Overview</h1>
      <h1 class="card">Notification</h1>
      <br>
      



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
  #userName {
    color: var(--color-text-primary);
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