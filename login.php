<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "grocerydb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

   
    $staffQuery = "SELECT * FROM staff WHERE username='$username' AND password='$password'";
    $staffResult = $conn->query($staffQuery);

    if ($staffResult->num_rows > 0) {
       
        $_SESSION['username'] = $username;
        $_SESSION['full_name'] = $staffResult->fetch_assoc()['full_name'];
        header("Location: staff_info.php");
        exit();
    }

    
$customerQuery = "SELECT * FROM customer WHERE username='$username' AND password='$password'";
$customerResult = $conn->query($customerQuery);

if ($customerResult->num_rows > 0) {
    
    $customerData = $customerResult->fetch_assoc();
    $_SESSION['username'] = $username;
    $_SESSION['full_name'] = $customerData['full_name'];
    $_SESSION['customer_id'] = $customerData['customer_id']; 
    header("Location: customer_product.php");
    exit();
} else {
    
    echo "Login Info Incorrect! Try Again " . $conn->error;
}


    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form action="" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    <br></br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</body>
<style>
    body {
        text-align: center;
        font-family:Times New Roman, Times, serif;
        padding-top: 250px;
        font-size: 20px;
        text-align: center;
    }

    body h2 {
        margin-top: 10px;
        font-size:40px;
    }
</style>
</html>
