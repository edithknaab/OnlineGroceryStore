<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$shippedOrdersQuery = "SELECT so.shipped_order_id, so.order_id, so.customer_id, c.full_name, DATE(so.order_date) as order_date, so.shipping_status
                       FROM shipped_orders so
                       JOIN customer c ON so.customer_id = c.customer_id
                       GROUP BY order_id, customer_id, shipping_status
                       ORDER BY order_date DESC";

$shippedOrdersResult = $conn->query($shippedOrdersQuery);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipped Orders</title>
</head>

<body>
    <h2>Shipped Orders</h2>

    <?php
    if ($shippedOrdersResult === false) {
        echo "Error: " . $conn->error;
    } elseif ($shippedOrdersResult->num_rows > 0) {
        while ($row = $shippedOrdersResult->fetch_assoc()) {
            echo "<p><strong>Shipped Order ID:</strong> " . $row["shipped_order_id"] . "</p>";
            echo "<p><strong>Order ID:</strong> " . $row["order_id"] . "</p>";
            echo "<p><strong>Customer ID:</strong> " . $row["customer_id"] . "</p>";
            echo "<p><strong>Customer Name:</strong> " . $row["full_name"] . "</p>";
            echo "<p><strong>Order Date:</strong> " . $row["order_date"] . "</p>";
            echo "<p><strong>Shipping Status:</strong> " . $row["shipping_status"] . "</p>";
            echo "<hr>";
        }
    } else {
        echo "No shipped orders available.";
    }

    if ($conn) {
        $conn->close();
    }
    ?>

    <p><a href="staff_info.php">Back to Staff Page</a></p>
</body>

</html>
