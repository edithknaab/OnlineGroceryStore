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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['store_name']) && isset($_POST['location'])) {
        $store_name = $_POST['store_name'];
        $location = $_POST['location'];

        
        $updateStoreQuery = "UPDATE store SET store_name = '$store_name', location = '$location' WHERE store_id = 1"; // Assuming store_id is 1

        if ($conn->query($updateStoreQuery) === TRUE) {
            echo "Store information updated successfully.";
        } else {
            echo "Error updating store information: " . $conn->error;
        }
    } else {
        echo "Invalid parameters provided.";
    }
}


$storeQuery = "SELECT * FROM store WHERE store_id = 1"; 
$storeResult = $conn->query($storeQuery);

if ($storeResult->num_rows > 0) {
    $row = $storeResult->fetch_assoc();
    $currentStoreName = $row["store_name"];
    $currentLocation = $row["location"];
} else {
    echo "No store information available.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Store Information</title>
    <style>
        body {
            font-family: Times New Roman, serif;
            margin: 20px;
        }
        form {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <nav>
        <a href="welcome.php">Home</a>
        <a href="logout.php">Logout</a>
    <h2>Edit Store Information</h2>

    <form method="POST" action="">
        <label for="store_name">Store Name:</label>
        <input type="text" id="store_name" name="store_name" value="<?php echo $currentStoreName; ?>" required>

        <label for="location">Location:</label>
        <input type="text" id="location" name="location" value="<?php echo $currentLocation; ?>" required>

        <button type="submit">Update Store Information</button>
    </form>
</body>
</html>
