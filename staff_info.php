<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();





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


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $searchStaffQuery = "SELECT * FROM staff WHERE full_name LIKE '%$searchQuery%' AND username != '" . $_SESSION['username'] . "'";
    $searchStaffResult = $conn->query($searchStaffQuery);
}


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['customer_search'])) {
    $searchCustomerQuery = "SELECT * FROM customer WHERE full_name LIKE '%" . $_GET['customer_search'] . "%'";
    $customerSearchResult = $conn->query($searchCustomerQuery);
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
        <a href='enter_hours.php'>Income Information</a>
        <a href="staff_edit_profile.php">Edit Profile</a>
        <a href="edit_store.php">Edit Store</a>
        <a href="logout.php">Log Out</a>
       
    </div>

    <h3>Staff Information:</h3>
    <form action="" method="post">
        <p><strong>Full Name:</strong> <?php echo $staffRow['full_name']; ?></p>
        <p><strong>Username:</strong> <?php echo $staffRow['username']; ?></p>
        <p><strong>Role:</strong> <?php echo $staffRow['role']; ?></p>
        <p><strong>Store ID:</strong> <?php echo $staffRow['store_id']; ?></p>
        <p><strong>Email:</strong> <?php echo $staffRow['email']; ?></p>
    </form>

    <form action="" method="get">
        <label for="search">Search Staff:</label>
        <input type="text" id="search" name="search" placeholder="Enter staff name">
        <button type="submit">Search Staff</button>
    </form>

    <?php
    if (isset($searchStaffResult) && $searchStaffResult->num_rows > 0) {
        echo "<h3>Search Results (Staff):</h3>";
        while ($row = $searchStaffResult->fetch_assoc()) {
            echo "<p><strong>Full Name:</strong> " . $row['full_name'] . " | <strong>Username:</strong> " . $row['username'] . " | <strong>Role:</strong> " . $row['role'] . " | <strong>Email:</strong> " . $row['email'] . "</p>";
        }
    }
    ?>

    <form action="" method="get">
        <label for="customer_search">Search Customers:</label>
        <input type="text" id="customer_search" name="customer_search" placeholder="Enter customer name">
        <button type="submit">Search Customers</button>
    </form>

<?php
if (isset($customerSearchResult) && $customerSearchResult->num_rows > 0) {
    echo "<h3>Search Results (Customers):</h3>";
    while ($row = $customerSearchResult->fetch_assoc()) {
        echo "<p><strong>Customer ID:</strong> " . $row['customer_id'] . " | <strong>Full Name:</strong> " . $row['full_name'] . " | <strong>Email:</strong> " . $row['email'] . " | <strong>Address:</strong> " . $row['address'] . "</p>";
    }
} elseif (isset($_GET['customer_search'])) {
    echo "<p>No customers found matching the search criteria.</p>";
}
?>

</body>
</html>
