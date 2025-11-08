<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      background-color: #f0f0f0; /* Optional: Add a subtle background to match a clean design */
    }

    .container {
      text-align: center;
      animation: fadeIn 1s ease-in-out; /* Slow mo fade-in over 3 seconds */
    }

    img {
      width: 30%;
      margin-bottom: 20px; /* Space between image and button */
    }

    button {
      padding: 10px 20px;
      font-size: 16px;
      background-color: #007bff; /* Blue to match a typical form design */
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s; /* Smooth hover effect */
    }

    button:hover {
      background-color: #0056b3; /* Darker blue on hover */
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px); /* Slight upward motion for a smoother effect */
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <img src="../img/formSubmitted.png" alt="Form Submitted">
    <br>
    <form action="report_violation.php">
    <button type="submit">Go Back</button> <!-- Example button; replace with desired action -->
    </form>
  </div>
</body>
</html>
