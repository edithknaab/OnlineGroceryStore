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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_order'])) {
    $orderIdToUpdate = $_POST['order_id'];
    $newQuantity = $_POST['new_quantity'];


    $updateOrderQuery = "UPDATE order_history SET quantity = $newQuantity WHERE order_id = $orderIdToUpdate";
    $conn->query($updateOrderQuery);

    header("Location: staff_view_orders.php");
    exit();
}

if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];
    $orderDetailsQuery = "SELECT order_history.*, customer.customer_id, customer.full_name, product.product_name
                          FROM order_history
                          INNER JOIN customer ON order_history.customer_id = customer.customer_id
                          INNER JOIN product ON order_history.product_id = product.product_id
                          WHERE order_history.order_id = $orderId";
    $orderDetailsResult = $conn->query($orderDetailsQuery);

    if ($orderDetailsResult->num_rows > 0) {
        $orderDetails = $orderDetailsResult->fetch_assoc();
    } else {
        echo "Order details not found.";
        exit();
    }
} else {
    echo "Order ID not provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Order</title>
</head>
<body>
    <h2>Update Order</h2>

    <?php
 
    echo "<p><strong>Customer ID:</strong> " . $orderDetails['customer_id'] . " | <strong>Full Name:</strong> " . $orderDetails['full_name'] . "</p>";
    echo "<p><strong>Product:</strong> " . $orderDetails["product_name"] . " | <strong>Quantity:</strong> " . $orderDetails["quantity"] . "</p>";

    
    echo "<form action='' method='post'>";
    echo "<input type='hidden' name='order_id' value='" . $orderDetails['order_id'] . "'>";
    echo "<label for='new_quantity'>New Quantity:</label>";
    echo "<input type='number' name='new_quantity' value='" . $orderDetails['quantity'] . "' required>";
    echo "<button type='submit' name='update_order'>Update Order</button>";
    echo "</form>";

    
    echo "<p><a href='staff_view_orders.php'>Back to Staff View Orders</a></p>";
    ?>

</body>
</html>

