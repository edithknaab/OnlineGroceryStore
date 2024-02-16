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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['remove_order'])) {
        $orderIdToRemove = $_POST['order_id'];

        $removeOrderQuery = "DELETE FROM order_history WHERE order_id = $orderIdToRemove";
        $conn->query($removeOrderQuery);

        header("Location: staff_view_orders.php");
        exit();
    } elseif (isset($_POST['update_order'])) {
        $orderIdToUpdate = $_POST['order_id'];

        header("Location: update_order.php?order_id=$orderIdToUpdate");
        exit();
    } elseif (isset($_POST['ship_order'])) {
        $orderIdToShip = $_POST['order_id'];

        $getOrderDetailsQuery = "SELECT * FROM order_history WHERE order_id = $orderIdToShip";
        $orderDetails = $conn->query($getOrderDetailsQuery)->fetch_assoc();

        $insertShippedOrderQuery = "INSERT INTO shipped_orders (order_id, customer_id, order_date, shipping_status)
                                    VALUES ($orderIdToShip, {$orderDetails['customer_id']}, '{$orderDetails['order_date']}', 'Shipped')";
        $conn->query($insertShippedOrderQuery);

        header("Location: staff_view_orders.php");
        exit();
    }
}

$orderHistoryQuery = "SELECT order_history.*, customer.customer_id, customer.full_name, product.product_name
                      FROM order_history
                      INNER JOIN customer ON order_history.customer_id = customer.customer_id
                      INNER JOIN product ON order_history.product_id = product.product_id
                      WHERE order_history.order_id NOT IN (SELECT order_id FROM shipped_orders)
                      ORDER BY order_history.order_date";

$orderHistoryResult = $conn->query($orderHistoryQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff View Orders</title>
</head>

<body>
    <h2>Staff View Orders</h2>

    <?php
    if (isset($orderHistoryResult) && $orderHistoryResult !== null && $orderHistoryResult->num_rows > 0) {
        $currentCustomerID = null;
        $currentOrderDateTime = null;

        while ($row = $orderHistoryResult->fetch_assoc()) {
            if ($row['customer_id'] !== $currentCustomerID) {
                echo "<h3>Customer ID: " . $row['customer_id'] . " | Full Name: " . $row['full_name'] . "</h3>";
                $currentCustomerID = $row['customer_id'];
            }

            $orderDateTime = $row["order_date"];
            if ($orderDateTime !== $currentOrderDateTime) {
                echo "<p><strong>Order Date:</strong> " . $orderDateTime . "</p>";
                $currentOrderDateTime = $orderDateTime;
            }

            echo "<p><strong>Product:</strong> " . $row["product_name"] . " | <strong>Quantity:</strong> " . $row["quantity"] . "</p>";

            echo "<form action='' method='post'>";
            echo "<input type='hidden' name='order_id' value='" . $row['order_id'] . "'>";

            
            if (isset($_POST['ship_order']) && $_POST['order_id'] == $row['order_id']) {
    

                continue;
            }

            echo "<button type='submit' name='ship_order'>Ship Order</button>";
            echo "<button type='submit' name='update_order'>Update Order</button>";
            echo "</form>";
        }
    } else {
        echo "No order history available.";
    }

    if ($conn) {
        $conn->close();
    }
    ?>

    <p><a href="staff_info.php">Back to Dashboard</a></p>
    <p><a href="shipped_orders.php">Shipped Orders</a></p>

</body>

</html>
