<?php 
session_start();
include'config/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        //fetch
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['lname'] . ',  ' . $user['fname'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['profile'] = $user['profile'];

        // Redirect by role
        if ($user['role'] == 'admin') {
            header("Location: dashboard.php?role=admin");
        } elseif ($user['role'] == 'teacher') {
            header("Location: dashboard.php?role=teacher");
        } elseif ($user['role'] == 'student') {
            header("Location: dashboard.php?role=student");
        } else {
            header("Location: dashboard.php?role=unknown");
        }
        exit;
    } else {
        echo "<script>alert('Invalid email or password'); window.location='index.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<img src="img/aclc-Photoroom.png" style="">
    <div class="form" >
        <form action="index.php" method="POST">
      <div class="title">Welcome</div>
      <div class="subtitle">Login to your account</div>
      <div class="input-container ic1">
        <input id="email" class="input" type="text" placeholder=" " name="email" required/>
        <div class="cut"></div>
        <label for="email" class="placeholder">Email</label>
      </div>
      <div class="input-container ic2">
        <input id="password" class="input" type="text" placeholder=" " name="password" required/>
        <div class="cut"></div>
        <label for="password" class="placeholder">password</label>
      </div>
      <button type="submit" class="submit">submit</button>
    </div>
    </form>
</body>
</html>

<style>
    body {
  align-items: center;
  /*
  background-color: rgb(26, 26, 26); */
  background: url(img/bg.jpg);
   background-repeat: no-repeat;
  background-attachment: fixed; 
  background-size: cover;
 
  display: flex;
  justify-content: center;
  height: 100vh;
}

.form {
  background-color: rgba(22, 22, 22, 1);
  border-radius: 20px;
  box-sizing: border-box;
  height: 450px;
  padding: 20px;
  width: 320px;
}

.title {
  color: #eee;
  font-family: sans-serif;
  font-size: 36px;
  font-weight: 600;
  margin-top: 30px;
}

.subtitle {
  color: #eee;
  font-family: sans-serif;
  font-size: 16px;
  font-weight: 600;
  margin-top: 10px;
}

.input-container {
  height: 50px;
  position: relative;
  width: 100%;
}

.ic1 {
  margin-top: 40px;
}

.ic2 {
  margin-top: 30px;
}

.input {
  background-color: #303245;
  border-radius: 12px;
  border: 0;
  box-sizing: border-box;
  color: #eee;
  font-size: 18px;
  height: 100%;
  outline: 0;
  padding: 4px 20px 0;
  width: 100%;
}

.cut {
  background-color: rgba(22, 22, 22, 1);
  border-radius: 10px;
  height: 20px;
  left: 20px;
  position: absolute;
  top: -20px;
  transform: translateY(0);
  transition: transform 200ms;
  width: 76px;
}

.cut-short {
  width: 50px;
}

.input:focus ~ .cut,
.input:not(:placeholder-shown) ~ .cut {
  transform: translateY(8px);
}

.placeholder {
  color: #65657b;
  font-family: sans-serif;
  left: 20px;
  line-height: 14px;
  pointer-events: none;
  position: absolute;
  transform-origin: 0 50%;
  transition: transform 200ms, color 200ms;
  top: 20px;
}

.input:focus ~ .placeholder,
.input:not(:placeholder-shown) ~ .placeholder {
  transform: translateY(-30px) translateX(10px) scale(0.75);
}

.input:not(:placeholder-shown) ~ .placeholder {
  color: #17171dff;
}

.input:focus ~ .placeholder {
  color: #dc2f55;
}

.submit {
  background-color: rgb(66, 184, 131);
  border-radius: 12px;
  border: 0;
  box-sizing: border-box;
  color: #eee;
  cursor: pointer;
  font-size: 18px;
  height: 50px;
  margin-top: 38px;

  text-align: center;
  width: 100%;
}

.submit:active {
  background-color: #06b;
}

/* Dashboard Styles */

</style>