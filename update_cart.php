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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'], $_POST['product_quantity'])) {
    $productId = $_POST['product_id'];
    $productQuantity = $_POST['product_quantity'];

    // Initialize $productName and $productPrice variables
    $productName = '';
    $productPrice = 0; // Set a default value, adjust as needed

    // Assuming you have a product_name and price column in the product table
    $productDataQuery = "SELECT product_name, price FROM product WHERE product_id = '$productId'";
    $productDataResult = $conn->query($productDataQuery);

    if ($productDataResult->num_rows > 0) {
        $productData = $productDataResult->fetch_assoc();
        $productName = $productData['product_name'];
        $productPrice = $productData['price'];

        // Update the shopping cart
        $customer_id = $_SESSION['customer_id'];
        $updateCartQuery = "INSERT INTO shopping_cart (customer_id, product_id, product_name, product_price, quantity, total, order_date) VALUES ('$customer_id', '$productId', '$productName', '$productPrice', '$productQuantity', '$productPrice' * '$productQuantity', NOW()) ON DUPLICATE KEY UPDATE quantity = quantity + '$productQuantity', total = '$productPrice' * quantity";
        $conn->query($updateCartQuery);

        // Update the product quantity in the product table
        $updateProductQuery = "UPDATE product SET stock_quantity = stock_quantity - '$productQuantity' WHERE product_id = '$productId'";
        $conn->query($updateProductQuery);

        echo "Product added to the cart successfully!";
    } else {
        echo "Product not found.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
