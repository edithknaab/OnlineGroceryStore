<?php
session_start();

if (!isset($_SESSION['username'])) {
    
    header("Location: login.php");
    exit();
}


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "grocerydb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$customerQuery = "SELECT * FROM customer WHERE username='" . $_SESSION['username'] . "'";
$customerResult = $conn->query($customerQuery);

if ($customerResult->num_rows > 0) {
    $customerData = $customerResult->fetch_assoc();
} else {
    
    $conn->close();
    die("Customer data not found.");
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_customer'])) {
    
    $newFullName = $_POST['full_name'];
    $newEmail = $_POST['email'];
    $newAddress = $_POST['address'];

   
    $updateCustomerQuery = "UPDATE customer SET full_name='$newFullName', email='$newEmail', address='$newAddress' WHERE username='" . $_SESSION['username'] . "'";
    $conn->query($updateCustomerQuery);
    
   
    header("Location: customer_dashboard.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['full_name']; ?></h2>

   
    <div style="position: absolute; top: 10px; right: 10px;">
        <a href="customer_product.php">Product Page</a>
        <a href="order_history.php">Order History</a>
        <a href="logout.php">Logout</a>
       
    </div>

    
    <h3>Customer Information:</h3>
    <form action="" method="post">
        <p><strong>Customer ID:</strong> <?php echo $customerData['customer_id']; ?></p>
        <p><strong>Full Name:</strong> <input type="text" name="full_name" value="<?php echo $customerData['full_name']; ?>"></p>
        <p><strong>Username:</strong> <?php echo $customerData['username']; ?></p>
        <p><strong>Email:</strong> <input type="email" name="email" value="<?php echo $customerData['email']; ?>"></p>
        <p><strong>Address:</strong> <input type="text" name="address" value="<?php echo $customerData['address']; ?>"></p>
        <p><strong>Password:</strong> <?php echo $customerData['password']; ?></p>
        <button type="submit" name="update_customer">Update Information</button>
    </form>

  

</body>
</html>
