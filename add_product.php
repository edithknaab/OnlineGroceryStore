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
    if (isset($_POST['add_product'])) {
        $product_name = $_POST['product_name'];
        $price = $_POST['price'];
        $stock_quantity = $_POST['stock_quantity'];

        $insertProductQuery = "INSERT INTO product (product_name, price, stock_quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertProductQuery);
        $stmt->bind_param("sdi", $product_name, $price, $stock_quantity);

        if ($stmt->execute()) {
            echo "Product added successfully!";
        } else {
            echo "Error adding product: " . $stmt->error;
        }

        $stmt->close();
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
</head>
<body>
    <h2>Add Product</h2>

    <form action="" method="post">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" required><br>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" step="0.01" required><br>

        <label for="stock_quantity">Stock Quantity:</label>
        <input type="number" id="stock_quantity" name="stock_quantity" required><br>

        <button type="submit" name="add_product">Add Product</button>
    </form>

    <p><a href="staff_add_delete_products.php">Back to Products</a></p>
    <p><a href="staff_info.php">Back to Staff Information</a></p>
    <p><a href="track_expenses.php">Track Expenses</a></p>
    <p><a href="logout.php">Log Out</a></p>
    
</body>
</html>