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


function formatCurrency($amount)
{
    return "$" . number_format($amount, 2);
}


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
    $productQuery = "SELECT * FROM product WHERE product_name LIKE '%$searchQuery%'";
} else {
    
    $productQuery = "SELECT * FROM product";
}

$productResult = $conn->query($productQuery);

$totalExpenses = 0; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Expenses</title>
</head>
<body>

<h2>Track Expenses</h2>

<!-- Search Bar -->
<form method="GET" action="">
    <label for="search">Search Product:</label>
    <input type="text" id="search" name="search" placeholder="Enter product name">
    <button type="submit">Search</button>
</form>

<?php

if ($productResult->num_rows > 0) {
    while ($row = $productResult->fetch_assoc()) {
        $productName = $row["product_name"];
        $price = $row["price"];
        $stockQuantity = $row["stock_quantity"];

        $totalCost = $price * $stockQuantity;

        echo "<p><strong>Product:</strong> $productName | <strong>Price:</strong> $" . $price . " | <strong>Stock Quantity:</strong> " . $stockQuantity . " | <strong>Total Cost:</strong> " . formatCurrency($totalCost) . "</p>";

        $totalExpenses += $totalCost;
    }

    
    echo "<p><strong>Total Expenses:</strong> " . formatCurrency($totalExpenses) . "</p>";
} else {
    echo "No products found in the database.";
}

$conn->close();
?>

<p><a href="staff_add_delete_products.php">Back to Products</a></p>
<p><a href="staff_info.php">Back to Staff Information</a></p>
<p><a href="logout.php">Log Out</a></p>

</body>
</html>