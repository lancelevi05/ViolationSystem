<?php
session_start();
include("../config/db_connect.php");

// Session check
if (!isset($_SESSION['user_id'])) {
  header('Location: ../index.php');
  exit();
}
  $strandData = mysqli_query($conn, "SELECT * FROM strandcourse_tbl WHERE shs_college =1");

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




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $usn = $_POST['usn'];
  $fname = $_POST['fname'];
  $lname = $_POST['lname'];
  $mname = $_POST['mname'];
  $strand = $_POST['strand'];

  $section = $_POST['section'];

  $gender = $_POST['gender'];
  $bdate = $_POST['bdate'];
  $glevel = $_POST['glevel'];

  // Use hidden inputs with names instead of codes
  $province = $_POST['province_name'];
  $city = $_POST['city_name'];
  $barangay = $_POST['barangay_name'];
  $address = $province . ', ' . $city . ', ' . $barangay;



  // Determine department based on strand
  if ($strand == 1 || $strand == 2 ) {
    $department = 1; // IT DEPARTMENT
  } elseif ($strand == 5) {
    $department = 2; // STEM DEPARTMENT
  } elseif ($strand == 4) {
    $department = 3; // HE DEPARTMENT
  } elseif($strand == 6) {
    $department = 4; // or any default value
  }
    
  // CHECK IF USN ALREADY EXISTS
$checkUSN = mysqli_query($conn, "SELECT usn FROM shs_tbl WHERE usn = '$usn' LIMIT 1");

if (mysqli_num_rows($checkUSN) > 0) {
    $_SESSION['errorMsg'] = "USN already exists in the system!";
    header("Location: admin_seniorhigh.php");
    exit();
}

  // Insert into database

  $sql = "INSERT INTO shs_tbl (usn, lname, fname, mname, idstrandcourse, glevel, section, genid, birthdate, address, iddepartment) 
            VALUES ('$usn', '$lname', '$fname', '$mname', '$strand','$glevel','$section','$gender', '$bdate', '$address', '$department')";

  if (mysqli_query($conn, $sql)) {
    // Set success message in session
    $_SESSION['successMsg'] = "Student added successfully!";
    // Redirect to the next page
    header("Location: admin_seniorhigh.php");
    exit();
  } else {
    $_SESSION['errorMsg'] = "Error: " . mysqli_error($conn);
    header("Location: admin_seniorhigh.php"); // redirect even if error
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
            <a href="#" class="menu-link dropdown-toggle active">
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
      <h1 class="page-title" style="display:none">Dashboard Overview</h1>
      <h1 class="card">Manage Student</h1>
      <br>


      <br>
      <section class="report-section">
        <div class="report-container">

          <!-- Left Image -->
          <div class="report-image">
            <img src="../img/ADMIN.png" alt="Report Image" style="width:500px">
          </div>

          <!-- Right Form -->
          <div class="report-form">
            <h2 class="report-title">ADD shs student Form</h2>

            <form action="admin_addstudent.php" method="POST" enctype="multipart/form-data">

              <!-- USN Input -->
              <div class="form-group">
  <label for="usn" class="form-label">USN</label>
  <input 
    type="text"
    id="usn"
    name="usn"
    placeholder="Enter student's USN"
    maxlength="11"
    inputmode="numeric"
    pattern="\d{11}"
    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
    required>
</div>

              <div class="divrow">

                

                <!-- lname Input -->
                <div class="form-group">
                  <label for="lname" class="form-label">Last Name</label>
                  <input type="text" id="lname" name="lname" placeholder="Enter valid first name" required>
                </div>
                <!-- fname Input -->
                <div class="form-group">
                  <label for="fname" class="form-label">First Name</label>
                  <input type="text" id="fname" name="fname" placeholder="Enter valid first name" required>
                </div>
                <!-- lname Input -->
                <div class="form-group">
                  <label for="mname" class="form-label">Middle name</label>
                  <input type="text" id="mname" name="mname" placeholder="Enter valid middle name">
                </div>

              </div>


              <div class="divrow">
<!-- Strand -->
              <div class="form-group">
               <label for="strand" class="form-label">Strand</label>
    <select id="strand" name="strand" required>
        <option value="">Select Strand</option>
        <?php while($s = mysqli_fetch_assoc($strandData)): ?>
            <option value="<?= $s['idstrandcourse']; ?>" data-max="<?= $s['max_section']; ?>">
                <?= $s['strandcourse']; ?>
            </option>
        <?php endwhile; ?>
    </select>
              </div>

               <!-- g level -->
              <div class="form-group">
                <label for="glevel" class="form-label">Grade Level</label>
                <select id="glevel" name="glevel" required>
                  <option value="11">11</option>
                  <option value="12">12</option>
                </select>
              </div>

<!-- Section -->
              <div class="form-group">
    <label for="section" class="form-label">Section</label>
    <select id="section" name="section" required>
        <option value="">Select Section</option>
    </select>
</div>

              </div>
              

               

              <!-- Gender -->
              <div class="form-group">
                <label for="gender" class="form-label">Gender</label>
                <select id="gender" name="gender" required>
                  <option value="1">Male</option>
                  <option value="2">Female</option>
             
                  
                </select>
              </div>

             

              <!-- bdate Input -->
              <div class="form-group">
                <label for="bdate" class="form-label">Birthdate</label>
                <input type="date" id="bdate" name="bdate" placeholder="Enter description" required>
              </div>

              <!-- address Input -->
              <div class="divrow">
                <!-- Province -->
                <div class="form-group">
                  <label for="province" class="form-label">Province</label>
                  <select id="province" name="province" class="form-control" required>
                    <option value="">Select Province</option>
                  </select>
                </div>

                <!-- City -->
                <div class="form-group">
                  <label for="city" class="form-label">City / Municipality</label>
                  <select id="city" name="city" class="form-control" required>
                    <option value="">Select City/Municipality</option>
                  </select>
                </div>

                <!-- Barangay -->
                <div class="form-group">
                  <label for="barangay" class="form-label">Barangay</label>
                  <select id="barangay" name="barangay" class="form-control" required>
                    <option value="">Select Barangay</option>
                  </select>
                </div>
              </div>
              <!-- Hidden inputs for names -->
              <input type="hidden" name="province_name" id="province_name">
              <input type="hidden" name="city_name" id="city_name">
              <input type="hidden" name="barangay_name" id="barangay_name">


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
// Set default birthdate to early 2000s (example: 2003-01-01)
document.addEventListener("DOMContentLoaded", function () {
    const bdate = document.getElementById("bdate");
    if (bdate) {
        bdate.value = "2003-01-01"; // change year as needed
    }
});


document.getElementById('strand').addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];
    const maxSection = selected.getAttribute('data-max');

    const sectionSelect = document.getElementById('section');
    sectionSelect.innerHTML = "<option value=''>Select Section</option>";

    if (maxSection) {
        const max = parseInt(maxSection);

        for (let i = 0; i < max; i++) {
            let letter = String.fromCharCode(65 + i); // A, B, C, D...
            let opt = document.createElement("option");
            opt.value = letter;
            opt.textContent = letter;
            sectionSelect.appendChild(opt);
        }
    }
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


    // Load provinces
    async function loadProvinces() {
      const res = await fetch("https://psgc.gitlab.io/api/provinces/");
      const data = await res.json();
      const provinceSelect = document.getElementById("province");
      data.sort((a, b) => a.name.localeCompare(b.name));
      data.forEach(province => {
        const opt = document.createElement("option");
        opt.value = province.code;
        opt.textContent = province.name;
        opt.setAttribute("data-name", province.name);
        provinceSelect.appendChild(opt);
      });
    }

    // Load cities when province changes
    document.getElementById("province").addEventListener("change", async function () {
      const provinceCode = this.value;
      const provinceName = this.options[this.selectedIndex].getAttribute("data-name");
      document.getElementById("province_name").value = provinceName || "";

      const citySelect = document.getElementById("city");
      const barangaySelect = document.getElementById("barangay");
      citySelect.innerHTML = "<option value=''>Select City/Municipality</option>";
      barangaySelect.innerHTML = "<option value=''>Select Barangay</option>";

      if (provinceCode) {
        const res = await fetch(`https://psgc.gitlab.io/api/provinces/${provinceCode}/cities-municipalities/`);
        const data = await res.json();
        data.sort((a, b) => a.name.localeCompare(b.name));
        data.forEach(city => {
          const opt = document.createElement("option");
          opt.value = city.code;
          opt.textContent = city.name;
          opt.setAttribute("data-name", city.name);
          citySelect.appendChild(opt);
        });
      }
    });

    // Load barangays when city changes
    document.getElementById("city").addEventListener("change", async function () {
      const cityCode = this.value;
      const cityName = this.options[this.selectedIndex].getAttribute("data-name");
      document.getElementById("city_name").value = cityName || "";

      const barangaySelect = document.getElementById("barangay");
      barangaySelect.innerHTML = "<option value=''>Select Barangay</option>";

      if (cityCode) {
        const res = await fetch(`https://psgc.gitlab.io/api/cities-municipalities/${cityCode}/barangays/`);
        const data = await res.json();
        data.sort((a, b) => a.name.localeCompare(b.name));
        data.forEach(barangay => {
          const opt = document.createElement("option");
          opt.value = barangay.code;
          opt.textContent = barangay.name;
          opt.setAttribute("data-name", barangay.name);
          barangaySelect.appendChild(opt);
        });
      }
    });

    // When barangay changes, update hidden input
    document.getElementById("barangay").addEventListener("change", function () {
      const barangayName = this.options[this.selectedIndex].getAttribute("data-name");
      document.getElementById("barangay_name").value = barangayName || "";
    });

    // Initialize provinces on page load
    loadProvinces();

    // Before form submit, copy names to hidden inputs
    document.querySelector("form").addEventListener("submit", function (e) {
      e.preventDefault(); // stop default submission

      const provinceSelect = document.getElementById("province");
      const citySelect = document.getElementById("city");
      const barangaySelect = document.getElementById("barangay");

      document.getElementById("province_name").value = provinceSelect.selectedOptions[0]?.getAttribute("data-name") || '';
      document.getElementById("city_name").value = citySelect.selectedOptions[0]?.getAttribute("data-name") || '';
      document.getElementById("barangay_name").value = barangaySelect.selectedOptions[0]?.getAttribute("data-name") || '';

      this.submit(); // submit form after setting hidden inputs
    });

    // Initialize provinces
    loadProvinces();



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
  #userName{
        color:var(--color-text-primary)
    }
  /* General Styles */
  body {

    font-family: "Poppins", sans-serif;

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

  input[type="text"],   input[type="date"],  
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
    background: var(--color-bg-form-input);
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
</style>