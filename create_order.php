<?php
session_start();


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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_to_cart'])) {
        $staff_id = $_SESSION['staff_id'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        
        $insertOrderQuery = "INSERT INTO staff_order (staff_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertOrderQuery);

        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        
        $stmt->bind_param("iii", $staff_id, $product_id, $quantity);

        
        if ($stmt->execute() === false) {
            die("Execute failed: " . $stmt->error);
        }

        echo "Order added successfully!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order</title>
</head>
<a href="staff_shopping_cart.php">Staff Shopping Cart</a>
<body>
    <h2>Welcome, Staff Member</h2>

    <h3>Available Products</h3>
    <form action="" method="post">
        <table border="1">
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
            <?php
            while ($row = $productResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['product_id'] . "</td>";
                echo "<td>" . $row['product_name'] . "</td>";
                echo "<td>$" . number_format($row['price'], 2) . "</td>";
                echo "<td><input type='number' name='quantity[" . $row['product_id'] . "]' value='1' min='1'></td>";
                echo "<td><button type='submit' name='add_to_cart' value='" . $row['product_id'] . "'>Add to Cart</button></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </form>
</body>
</html>
