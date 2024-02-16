<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

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

$staffQuery = "SELECT * FROM staff WHERE username='" . $_SESSION['username'] . "'";
$staffResult = $conn->query($staffQuery);

if ($staffResult->num_rows > 0) {
    $staffRow = $staffResult->fetch_assoc();
    $staffId = $staffRow['staff_id'];
    $hourlyRate = isset($staffRow['hourly_rate']) ? floatval($staffRow['hourly_rate']) : 20.00; 
    $payReccurance = isset($staffRow['pay_reccurance']) ? $staffRow['pay_reccurance'] : '';

    
    $_SESSION['staff_id'] = $staffId;
} else {
    
    $conn->close();
    die("Staff information not found.");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['search_date'])) {
        $searchDate = $_POST['search_date'];
        $searchDateFormatted = date('Y-m-d', strtotime($searchDate));

        
        $incomeHistoryQuery = "SELECT * FROM income WHERE staff_id = ? AND date_worked = ?";
        $stmt = $conn->prepare($incomeHistoryQuery);

        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        
        $stmt->bind_param("is", $staffId, $searchDateFormatted);

        
        if ($stmt->execute() === false) {
            die("Execute failed: " . $stmt->error);
        }

        
        $result = $stmt->get_result();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income History</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(function () {
            $("#datepicker").datepicker();
        });
    </script>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>

    <h3>Income History</h3>

    <form action="" method="post">
        <label for="datepicker">Search by Date:</label>
        <input type="text" id="datepicker" name="search_date" required>
        <button type="submit">Search</button>
    </form>

    <?php
    if (isset($result) && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<p><strong>Date Worked:</strong> " . $row['date_worked'] . "</p>";
            echo "<p><strong>Hours Worked:</strong> " . $row['hours_worked'] . "</p>";
            echo "<p><strong>Total Income:</strong> $" . $row['total_income'] . "</p>";
            echo "<hr>";
        }
    } else {
        echo "<p>No income history found.</p>";
    }
    
    ?>
    <p><a href="staff_info.php">Back to Staff Information</a></p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
