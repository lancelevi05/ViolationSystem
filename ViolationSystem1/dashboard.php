<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$role = $_SESSION['role'];
$name = $_SESSION['user_name'];
$wid =   $_SESSION['user_id'];
$profile = $_SESSION['profile'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
</head>
<body>
  <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>

  <?php if ($role == 'admin'):
     header('Location: admin/admin_dashboard.php');
    ?>
    <h3>Admin Dashboard</h3>
    <p>You can manage users, violations, and reports.</p>

  <?php elseif ($role == 'teacher'):
  header('Location: teacher/teacher_dashboard.php');
    ?>
    <h3>Teacher Dashboard</h3>
    <p>You can view student violations and submit reports.</p>




  <?php elseif ($role == 'Guidance'): 
    header('Location: guidance/guidance_dashboard.php');
    
    ?>
    

  <?php else: 
    header('Location: nonteacher/nonteacher_dashboard.php');?>
    <h3>Unknown Role</h3>
    <p>Your role is not recognized. Please contact admin.</p>
  <?php endif; ?>

  <a href="logout.php">Logout</a>
</body>
</html>
