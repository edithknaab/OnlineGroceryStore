<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "grocerydb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $customer_id = $_SESSION['customer_id'];
    $cartQuery = "SELECT * FROM shopping_cart WHERE customer_id = $customer_id";
    $cartResult = $conn->query($cartQuery);


if ($cartResult->num_rows > 0) {
    $totalPrice = 0;

    while ($row = $cartResult->fetch_assoc()) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];
        $order_date = $row['order_date'];

        $totalPrice += $row['product_price'] * $quantity;

        $orderHistoryQuery = "INSERT INTO order_history (customer_id, product_id, quantity, order_date) VALUES ('$customer_id', '$product_id', '$quantity', '$order_date')";
        $conn->query($orderHistoryQuery);
    }

$_SESSION['total_price'] = $totalPrice;

$clearCartQuery = "DELETE FROM shopping_cart WHERE customer_id = $customer_id";
$conn->query($clearCartQuery);

header("Location: checkout_confirmation.php");
exit();


    $clearCartQuery = "DELETE FROM shopping_cart WHERE customer_id = $customer_id";
    $conn->query($clearCartQuery);

    echo "Checkout successful! Total Price: $" . number_format($totalPrice, 2);

    header("Location: checkout_confirmation.php");
    exit();
} else {
    echo "Shopping cart is empty.";
}

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Product Page</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>

    <div style="position: absolute; top: 10px; right: 10px;">
        <a href="logout.php">Logout</a>
        <a href="staff_info.php">Back to Staff Information</a>
        

    </div>

    <?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "grocerydb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $productQuery = "SELECT * FROM product";
    $productResult = $conn->query($productQuery);

    if ($productResult->num_rows > 0) {
        echo "<h3>Available Products:</h3>";
        while ($row = $productResult->fetch_assoc()) {
            echo "<p><strong>Product:</strong> " . $row["product_name"] . " | <strong>Price:</strong> $" . $row["price"] . " | <button onclick=\"addToCart(" . $row["product_id"] . ", '" . $row["product_name"] . "', " . $row["price"] . ")\">Add to Cart</button></p>";
        }
    } else {
        echo "No products available.";
    }

    $conn->close();
    ?>

    <script>
    function addToCart(productId, productName, productPrice) {
        var productQuantity = prompt("Enter quantity:", 1);

        if (!productQuantity || isNaN(productQuantity) || productQuantity <= 0) {
            alert("Invalid quantity. Please enter a valid number greater than 0.");
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "update_cart.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    alert(xhr.responseText);
                } else {
                    alert("Error adding product to cart. Status: " + xhr.status);
                }
            }
        };
        xhr.send("product_id=" + productId + "&product_name=" + productName + "&product_price=" + productPrice + "&product_quantity=" + productQuantity);
    }
</script>

</body>
</html>