<?php 
session_start();
include 'config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Secure prepared statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $user['password'])) {

            // Store session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['lname'] . ', ' . $user['fname'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['profile'] = $user['profile'];

            // Redirect by role
            switch ($user['role']) {
                case 'admin':
                    header("Location: dashboard.php?role=admin");
                    break;
                case 'teacher':
                    header("Location: dashboard.php?role=teacher");
                    break;
                case 'Guidance':
                    header("Location: dashboard.php?role=Guidance");
                    break;
                default:
                    header("Location: dashboard.php?role=unknown");
                    break;
            }
            exit;

        } else {
            $_SESSION['errorMsg'] = "Incorrect password!";
        }

    } else {
        $_SESSION['errorMsg'] = "Email not found!";
    }
}

$successMsg = $_SESSION['successMsg'] ?? '';
$errorMsg = $_SESSION['errorMsg'] ?? '';

unset($_SESSION['successMsg']);
unset($_SESSION['errorMsg']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/x-icon" href="img/aclc-Photoroom.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body>

    <div class="login-container">

        <div class="logo-wrapper">
            <img src="img/aclc-Photoroom.png" class="logo">
        </div>

        <form action="index.php" method="POST" class="login-card">

            <h1 class="title">Welcome</h1>
            <p class="subtitle">Login to your account</p>

            <div class="input-group">
                <input type="text" name="email" id="email" required>
                <label>Email</label>
            </div>

            <div class="input-group">
                <input type="password" name="password" id="password" required>
                <label>Password</label>
            </div>

            <button type="submit" class="submit-btn">Login</button>

            <?php if (!empty($errorMsg)): ?>
                <div class="alert error">
                    <?= $errorMsg ?>
                </div>
            <?php endif; ?>

        </form>
    </div>

</body>
</html>

<style>

body {
    margin: 0;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;

    background: url("img/footerACLC.jpg") no-repeat center center fixed;
    background-size: cover;
    font-family: "Poppins", sans-serif;
}

.login-container {
    text-align: center;
}

.logo {
    width: 120px;
    margin-bottom: 15px;
    animation: fadeDown 0.8s ease;
}

@keyframes fadeDown {
    from { opacity: 0; transform: translateY(-20px); }
    to   { opacity: 1; transform: translateY(0); }
}

.login-card {
    width: 330px;
    padding: 30px 25px;
    background: #1e1e1e; /* <--- CHANGE COLOR HERE */
    border-radius: 18px;
    /* backdrop-filter: blur(12px);  REMOVE THIS */
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    animation: fadeUp 0.8s ease;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}

.title {
    color: #fff;
    font-size: 32px;
    margin: 10px 0 5px;
}

.subtitle {
    color: #d3d3d3;
    margin-bottom: 25px;
}

.input-group {
    position: relative;
    margin-bottom: 25px;
}

.input-group input {
    width: 90%;
    padding: 14px 12px 12px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255,255,255,0.3);
    border-radius: 12px;
    color: #fff;
    font-size: 16px;
    outline: none;
    transition: 0.3s;
}

.input-group input:focus {
    border-color: #00e676;
    box-shadow: 0 0 6px #00e676;
}

.input-group label {
    position: absolute;
    top: 50%;
    left: 14px;
    transform: translateY(-50%);
    color: #d0d0d0;
    pointer-events: none;
    transition: 0.3s;
    background: transparent;
    padding: 0 4px;
}

.input-group input:focus + label,
.input-group input:not(:placeholder-shown) + label {
    top: -9px;
    font-size: 12px;
    color: #00e676;
}

.submit-btn {
    width: 100%;
    padding: 12px;
    border: none;
    background: #00c853;
    color: white;
    font-size: 17px;
    border-radius: 12px;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 5px;
}

.submit-btn:hover {
    background: #00e676;
    transform: scale(1.03);
}

.alert {
    margin-top: 15px;
    padding: 10px;
    border-radius: 10px;
    font-weight: 500;
}

.alert.error {
    color: #ff5252;
    background: rgba(255, 82, 82, 0.17);
    border: 1px solid rgba(255, 82, 82, 0.4);
}

</style>
