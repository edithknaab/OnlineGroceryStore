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

$staffQuery = "SELECT * FROM staff WHERE username='" . $_SESSION['username'] . "'";
$staffResult = $conn->query($staffQuery);

if ($staffResult->num_rows > 0) {
    $staffRow = $staffResult->fetch_assoc();
} else {
    $conn->close();
    die("Staff information not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_staff'])) {
    $newFullName = $_POST['full_name'];
    $newEmail = $_POST['email'];

    $updateStaffQuery = "UPDATE staff SET full_name='$newFullName', email='$newEmail' WHERE username='" . $_SESSION['username'] . "'";
    $conn->query($updateStaffQuery);

    header("Location: staff_info.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Information</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>

    <div style="position: absolute; top: 10px; right: 10px;">
        <a href="staff_view_orders.php">View Orders</a>
        <a href="add_product.php">Add Product</a>
        <a href="delete_product.php">Delete Product</a>
        <a href='income_history.php'>Income History</a>
        <a href="logout.php">Log Out</a>
    </div>

    <h3>Staff Information:</h3>
    <form action="" method="post">
        <p><strong>Full Name:</strong> <input type="text" name="full_name" value="<?php echo $staffRow['full_name']; ?>"></p>
        <p><strong>Username:</strong> <?php echo $staffRow['username']; ?></p>
        <p><strong>Role:</strong> <?php echo $staffRow['role']; ?></p>
        <p><strong>Store ID:</strong> <?php echo $staffRow['store_id']; ?></p>
        <p><strong>Email:</strong> <input type="email" name="email" value="<?php echo $staffRow['email']; ?>"></p>
        <button type="submit" name="update_staff">Update Information</button>
    </form>


</body>
</html>
