<?php
session_start();
include("../config/db_connect.php");

// Ensure only admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$name = $_SESSION['user_name'];
$profile = $_SESSION['profile'];

// Update current user's last activity
$current_user_id = $_SESSION['user_id'];
mysqli_query($conn, "UPDATE users SET last_activity = CURRENT_TIMESTAMP WHERE id = $current_user_id");

// Fetch all users with their online status
$query = "SELECT *, 
          CASE 
            WHEN last_activity >= NOW() - INTERVAL 5 MINUTE THEN 1 
            ELSE 0 
          END as is_online 
          FROM users 
          ORDER BY last_activity DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgb(26, 26, 26);

            /*
  background-color: rgb(26, 26, 26); */
            background: url(../img/bg.jpg);
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
        }

        .user-card {
            position: relative;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 10px;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: rgba(68, 68, 68, 1);
        }

        .status-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
        }

        .status-online {
            background-color: #28a745;
            box-shadow: 0 0 0 2px #28a745;
        }

        .status-offline {
            background-color: #6c757d;
            box-shadow: 0 0 0 2px #6c757d;
        }

        .user-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
        }

        .last-active {
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
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
                        <a class="nav-link" href="admin_dashboard.php" style="color:white">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="user_management.php" style="color:white">Users</a>
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

    <div class="container mt-4">
        <h2 class="mb-4">User Management</h2>

        <div class="row">
            <?php while ($user = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="user-card">
                        <!-- Status Indicator -->
                        <span
                            class="status-indicator <?php echo $user['is_online'] ? 'status-online' : 'status-offline' ?>"></span>

                        <div class="d-flex align-items-center">
                            <!-- User Avatar -->
                            <img src="<?php echo htmlspecialchars($user['profile']) ?>"
                                alt="<?php echo htmlspecialchars($user['fname']) ?>" class="user-avatar me-3">

                            <div>
                                <!-- User Info -->
                                <h5 class="mb-1" style="color:rgb(66, 184, 131)">
                                    <?php echo $user['fname'] . ' ' . $user['lname'] ?>
                                </h5>
                                <p class="mb-1 " style="color:rgba(236, 236, 236, 1);">
                                    <?php echo $user['role'] ?>
                                </p>
                                <p class="mb-0 last-active">
                                    <?php
                                    if ($user['is_online']) {
                                        echo "<h9 style='color:green'>Online now</h9>";
                                    } else {
                                        $last_active = strtotime($user['last_activity']);
                                        $now = time();
                                        $diff = $now - $last_active;

                                        if ($diff < 60) {
                                            echo 'Last seen just now';
                                        } elseif ($diff < 3600) {
                                            echo 'Last seen ' . floor($diff / 60) . ' minutes ago';
                                        } elseif ($diff < 86400) {
                                            echo 'Last seen ' . floor($diff / 3600) . ' hours ago';
                                        } else {
                                            echo 'Last seen ' . floor($diff / 86400) . ' days ago';
                                        }
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh the page every 30 seconds to update online status
        setInterval(function () {
            window.location.reload();
        }, 30000);
    </script>
</body>

</html>