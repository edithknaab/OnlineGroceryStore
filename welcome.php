<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Owl Shop</title>
    <style>
    
        body {
            font-family: Times New Roman, Times, serif;
            margin: 220px;
            text-align: center;
        }
        
        nav {
            display: flex;
            align-items: center;
        }
        nav a {
            margin-right: 15px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1><strong>Online Grocery Store</strong></h1>
    <section>
        <h2>Our Store Information</h2>
        <?php

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "grocerydb";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $storeQuery = "SELECT * FROM store";
        $storeResult = $conn->query($storeQuery);

        if ($storeResult->num_rows > 0) {
            while ($row = $storeResult->fetch_assoc()) {
                echo "<p><strong>Store Name:</strong> " . $row["store_name"] . "</p>";
                echo "<p><strong>Location:</strong> " . $row["location"] . "</p>";
            }
        } else {
            echo "No store information available.";
        }

        $conn->close();
        ?>
    </section>
    <a href="login.php">Login</a>
</body>
</html>
