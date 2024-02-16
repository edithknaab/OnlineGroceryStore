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
    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];

        $deleteProductQuery = "DELETE FROM product WHERE product_id = ?";
        $stmt = $conn->prepare($deleteProductQuery);
        $stmt->bind_param("i", $product_id);

        if ($stmt->execute()) {
           
            echo "Product deleted successfully!";
        } else {
            
            echo "Error deleting product: " . $stmt->error;
        }

        $stmt->close();
    }
}


$productListQuery = "SELECT product_id, product_name FROM product";
$productListResult = $conn->query($productListQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Product</title>
</head>
<body>
    <h2>Delete Product</h2>

    <form action="" method="post">
        <label for="product_id">Select a Product:</label>
        <select id="product_id" name="product_id" required>
            <?php
            while ($row = $productListResult->fetch_assoc()) {
                echo "<option value='" . $row['product_id'] . "'>" . $row['product_name'] . "</option>";
            }
            ?>
        </select><br>

        <button type="submit" name="delete_product">Delete Product</button>
    </form>

    <p><a href="staff_add_delete_products.php">Back to Products</a></p>
    <p><a href="track_expenses.php">Track Expenses</a></p>
</body>
</html>

<?php

if ($conn) {
    $conn->close();
}
?>
