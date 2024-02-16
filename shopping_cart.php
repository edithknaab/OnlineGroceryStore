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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['checkout'])) {
        
        $customer_id = $_SESSION['customer_id'];
        $cartQuery = "SELECT shopping_cart.*, product.product_name, product.price FROM shopping_cart INNER JOIN product ON shopping_cart.product_id = product.product_id WHERE shopping_cart.customer_id = $customer_id";
        $cartResult = $conn->query($cartQuery);

        
        if ($cartResult->num_rows > 0) {
            
            $totalPrice = 0;
            $orderDetails = "";

            while ($row = $cartResult->fetch_assoc()) {
                $totalPrice += $row['price'] * $row['quantity'];
                $orderDetails .= "Product: " . $row["product_name"] . " | Quantity: " . $row["quantity"] . "\n";

               
                $insertOrderHistoryQuery = "INSERT INTO order_history (customer_id, product_id, quantity, order_date) VALUES (?, ?, ?, NOW())";
                $stmt = $conn->prepare($insertOrderHistoryQuery);
                $stmt->bind_param("iii", $customer_id, $row['product_id'], $row['quantity']);
                $stmt->execute();
            }

            
            $_SESSION['total_price'] = $totalPrice;
            $_SESSION['order_details'] = $orderDetails;

           
            $clearCartQuery = "DELETE FROM shopping_cart WHERE customer_id = $customer_id";
            $conn->query($clearCartQuery);

            header("Location: checkout_confirmation.php");
            exit();
        } else {
            echo "Shopping cart is empty. Cannot proceed with checkout.";
        }
    } elseif (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $product_id => $new_quantity) {
            if (!is_numeric($new_quantity) || $new_quantity < 1) {
                echo "Invalid quantity for product ID: $product_id";
                exit();
            }

            $updateQuantityQuery = "UPDATE shopping_cart SET quantity = $new_quantity WHERE customer_id = {$_SESSION['customer_id']} AND product_id = $product_id";
            $conn->query($updateQuantityQuery);
        }

        header("Location: shopping_cart.php");
        exit();
    } elseif (isset($_POST['remove_from_cart'])) {
        $product_id_to_remove = $_POST['product_id_to_remove'];

        $removeFromCartQuery = "DELETE FROM shopping_cart WHERE customer_id = {$_SESSION['customer_id']} AND product_id = $product_id_to_remove";
        $conn->query($removeFromCartQuery);

        header("Location: shopping_cart.php");
        exit();
    }
}

$customer_id = $_SESSION['customer_id'];
$cartQuery = "SELECT shopping_cart.*, product.product_name, product.price FROM shopping_cart INNER JOIN product ON shopping_cart.product_id = product.product_id WHERE shopping_cart.customer_id = $customer_id";
$cartResult = $conn->query($cartQuery);

if ($cartResult->num_rows > 0) {
    echo "<h3>Cart Items:</h3>";
    $totalPrice = 0;
    $orderDetails = "";

    while ($row = $cartResult->fetch_assoc()) {
        $productName = $row["product_name"];
        $price = $row["price"];
        $quantity = $row["quantity"];
        $product_id = $row["product_id"];

        echo "<form action='' method='post'>";
        echo "<p><strong>Product:</strong> $productName | <strong>Price:</strong> $$price | <strong>Quantity:</strong> ";
        echo "<input type='number' name='quantity[$product_id]' value='$quantity' min='1'>";
        echo "<button type='submit' name='update_cart'>Update</button>";
        echo "</p>";
        echo "</form>";

        echo "<form action='' method='post'>";
        echo "<input type='hidden' name='product_id_to_remove' value='$product_id'>";
        echo "<button type='submit' name='remove_from_cart'>Remove</button>";
        echo "</form>";

        $totalPrice += $price * $quantity;
        $orderDetails .= "Product: $productName | Quantity: $quantity\n";
    }

    $_SESSION['total_price'] = $totalPrice;
    $_SESSION['order_details'] = $orderDetails;

    echo "<p><strong>Total Price:</strong> $" . number_format($totalPrice, 2) . "</p>";
    echo "<form action='' method='post'>";  
    echo "<button type='submit' name='checkout'>Checkout</button>";
    echo "</form>";
} else {
    echo "Shopping cart is empty.";
}


$conn->close();
?>
