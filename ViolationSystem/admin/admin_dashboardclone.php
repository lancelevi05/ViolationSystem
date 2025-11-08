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

// === FETCH DATA REQUEST ===
if (isset($_GET['fetchData'])) {
  $month = $_GET['month'] ?? '';
  $year = $_GET['year'] ?? '';

  if (!$month || !$year) {
    echo json_encode(['labels' => [], 'values' => [], 'total' => 0, 'message' => 'Please select both month and year.']);
    exit;
  }

  $sql = "
        SELECT DAY(`date`) AS day, COUNT(*) AS total
        FROM violation
        WHERE MONTH(`date`) = '$month' 
          AND YEAR(`date`) = '$year'
        GROUP BY DAY(`date`)
        ORDER BY day ASC
    ";

  $result = mysqli_query($conn, $sql);
  $labels = [];
  $values = [];

  while ($row = mysqli_fetch_assoc($result)) {
    $labels[] = $row['day'];
    $values[] = $row['total'];
  }

  if (empty($labels)) {
    echo json_encode(['labels' => [], 'values' => [], 'total' => 0, 'message' => 'No records of violation.']);
  } else {
    $total = array_sum($values);
    echo json_encode(['labels' => $labels, 'values' => $values, 'total' => $total, 'message' => 'success']);
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
</head>

<body style="background-color:rgb(26,26,26); background:url(../img/bg.jpg) no-repeat fixed; background-size:cover;">

  <!-- NAVIGATION BAR -->
  <nav class="navbar navbar-expand-lg bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand text-white"><?php echo htmlspecialchars($name); ?></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarScroll">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link text-white" href="admin_dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="user_management.php">Users</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="../logout.php">Log Out</a></li>
        </ul>
        <div class="d-flex">
          <img src="<?php echo htmlspecialchars($profile); ?>"
            style="height:50px;width:50px;object-fit:cover;border-radius:50%;">
        </div>
      </div>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
 
    <center>
      <h2 style="color:rgba(48,133,94,1);">Summary</h2>
    </center>
    <h3 style="color:rgba(38,104,74,1);">Campus Violations by Month / Year</h3>

    <form id="filterForm" onsubmit="return false;" class="mb-3">
      <label for="month"><b>Month:</b></label>
      <select id="month" name="month" required>
        <option value="">--Select Month--</option>
        <option value="01">January</option>
        <option value="02">February</option>
        <option value="03">March</option>
        <option value="04">April</option>
        <option value="05">May</option>
        <option value="06">June</option>
        <option value="07">July</option>
        <option value="08">August</option>
        <option value="09">September</option>
        <option value="10">October</option>
        <option value="11">November</option>
        <option value="12">December</option>
      </select>

      <label for="year">Year:</label>
      <select id="year" name="year" required>
        <option value="">--Select Year--</option>
      </select>

      <button type="submit" class="btn btn-success btn-sm ms-2">Fetch Data</button>
    </form>

    <div id="message" style="color:orange; font-weight:bold;"></div>

    <canvas id="myChart" style="width:60%;max-width:650px;height:100px;background-color:rgba(36,36,36,1);"></canvas>
    <h4 id="totalCount" style="color:white;margin-top:10px;">Total violations this month: 0</h4>


  <!-- JS LOGIC -->
  <script>
    // Populate years dropdown (2020–2030)
    const yearSelect = document.getElementById('year');
    for (let y = 2020; y <= 2030; y++) {
      yearSelect.innerHTML += `<option value="${y}">${y}</option>`;
    }

    // Initialize Chart.js
    const ctx = document.getElementById('myChart').getContext('2d');
    let myChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: [],
        datasets: [{
          label: 'Violations per Day',
          data: [],
          backgroundColor: 'rgb(66, 184, 131)',
          borderColor: 'rgba(2, 2, 2, 1)',
          borderWidth: 1
        }]
      },
      options: {
        plugins: {
          legend: { display: false },
          //
          title: {
            display: true,
        text: "",
        font: {size: 16,}
         }, //,
          tooltip: {
            callbacks: {
              title: function (context) {
                return 'Day: ' + context[0].label;
              },
              label: function (context) {
                return 'Total violation: ' + context.raw;
              }
            }
          }
        },
        scales: {
          y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
      }
    });

    // Fetch Data Function
    async function fetchData(month, year) {
      const msg = document.getElementById('message');
      const totalText = document.getElementById('totalCount');
      msg.textContent = "Loading...";

      const response = await fetch(`admin_dashboard.php?fetchData=1&month=${month}&year=${year}`);
      const data = await response.json();

      if (data.message && data.message !== "success") {
        msg.textContent = data.message;
        myChart.data.labels = [];
        myChart.data.datasets[0].data = [];
        myChart.update();
        totalText.textContent = "Total violations this month: 0";
      } else {
        msg.textContent = "";
        myChart.data.labels = data.labels;
        myChart.data.datasets[0].data = data.values;
        myChart.update();

        const total = data.total ?? data.values.reduce((a, b) => a + parseInt(b), 0);
        totalText.textContent = "Total violations this month: " + total;
      }
    }

    // Handle form submit
    document.getElementById('filterForm').addEventListener('submit', async function () {
      const month = document.getElementById('month').value;
      const year = document.getElementById('year').value;

      if (!month || !year) {
        alert("Please select both month and year!");
        return;
      }

      localStorage.setItem('selectedMonth', month);
      localStorage.setItem('selectedYear', year);

      fetchData(month, year);
    });

    // Load previous selections on refresh
    window.addEventListener('load', () => {
      const savedMonth = localStorage.getItem('selectedMonth');
      const savedYear = localStorage.getItem('selectedYear');

      if (savedMonth) document.getElementById('month').value = savedMonth;
      if (savedYear) document.getElementById('year').value = savedYear;

      if (savedMonth && savedYear) fetchData(savedMonth, savedYear);
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

