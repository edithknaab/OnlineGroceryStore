<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['total_price']) && isset($_SESSION['order_details'])) {

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "grocerydb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    echo "<h2>Checkout Confirmation</h2>";
    echo "<p> Order Recieved!</p>";
    echo "<p>Order Details:</p>";
    echo "<pre>{$_SESSION['order_details']}</pre>";
    echo "<p>Total Price: $" . number_format($_SESSION['total_price'], 2) . "</p>";
    $conn->close();
} else {
    echo "Session variables are not set. Please go back and try again.";
}
?>

<nav>
    <a href="customer_product.php">Product Page</a>
    <a href="order_history.php">Order History</a>
    <a href="logout.php">Logout</a>
    <a href="shopping_cart.php">Shopping Cart</a>
</nav>
