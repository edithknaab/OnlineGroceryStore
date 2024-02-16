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

$customer_id = $_SESSION['customer_id'];
$orderHistoryQuery = "SELECT order_history.*, product.product_name, product.price, DATE_FORMAT(order_history.order_date, '%Y-%m-%d %H:%i:%s') AS formatted_date FROM order_history INNER JOIN product ON order_history.product_id = product.product_id WHERE order_history.customer_id = $customer_id ORDER BY order_history.order_date";
$orderHistoryResult = $conn->query($orderHistoryQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
</head>
<body>
    <h2>Order History</h2>

    <?php
if ($orderHistoryResult->num_rows > 0) {
    $currentDate = null;
    $totalPrice = 0;

    while ($row = $orderHistoryResult->fetch_assoc()) {
        $orderDate = $row["formatted_date"];
    
        
        if ($orderDate !== $currentDate) {
            if ($currentDate !== null) {
                
                echo "<p><strong>Total Price:</strong> $" . number_format($totalPrice, 2) . "</p>";
                echo "</p>";
                $totalPrice = 0;
            }
            echo "<h3>Order made on $orderDate</h3>";
            $currentDate = $orderDate;
        }
    
        
        echo "<p><strong>Product:</strong> " . $row["product_name"] . " | <strong>Quantity:</strong> " . $row["quantity"] . " | <strong>Price:</strong> $" . number_format($row["price"], 2) . "</p>";
    
        
        echo "<form id='updateForm_" . $row['order_id'] . "' onsubmit='return false;'>";
        echo "<input type='hidden' name='order_id' value='" . $row['order_id'] . "'>";

        echo "</form>";
    
        
        $totalPrice += $row["price"] * $row["quantity"];
    }

    
    if ($currentDate !== null) {
        echo "<p><strong>Total Price:</strong> $" . number_format($totalPrice, 2) . "</p>";
    }
} else {
    echo "No order history available.";
}


if ($conn) {
    $conn->close();
}
?>

<script>
    function submitUpdate(orderDate, orderId) {
        var form = document.getElementById('updateForm_' + orderId);
        var formData = new FormData(form);

       
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    
                    console.log(xhr.responseText);

                    
                    var response = JSON.parse(xhr.responseText);

                    
                    updateOrderDetails(orderId, response.quantity, response.price);

                    
                    updateTotalPrice(orderDate, response.totalPrice);

                    
                    location.reload();
                } else {
                    
                    console.error('Error:', xhr.status, xhr.statusText);
                }
            }
        };

        xhr.open('POST', 'process_update_order.php?order_date=' + orderDate, true);
        xhr.send(formData);
    }

    function updateOrderDetails(orderId, newQuantity, newPrice) {
        
        var quantityElement = document.getElementById('quantity_' + orderId);
        var priceElement = document.getElementById('price_' + orderId);

        if (quantityElement && priceElement) {
            quantityElement.textContent = newQuantity;
            priceElement.textContent = '$' + newPrice.toFixed(2);
        }
    }

    function updateTotalPrice(orderDate, newTotalPrice) {
       
        var totalPriceElement = document.getElementById('totalPrice_' + orderDate);

        if (totalPriceElement) {
            totalPriceElement.textContent = 'Total Price: $' + newTotalPrice.toFixed(2);
        }
    }
</script>


    <p><a href="customer_product.php">Back to Products</a></p>
</body>
</html>
