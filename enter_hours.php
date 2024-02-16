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
    if (isset($_POST['hours_worked'])) {
        $hoursWorked = isset($_POST['hours_worked']) ? floatval($_POST['hours_worked']) : 0.00;
        $totalIncome = $hourlyRate * $hoursWorked;

        
        $insertHoursQuery = "INSERT INTO income (staff_id, hourly_rate, date_worked, hours_worked, total_income, pay_reccurance) VALUES (?, ?, NOW(), ?, ?, ?)";
        $stmt = $conn->prepare($insertHoursQuery);
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        
        $stmt->bind_param("iddds", $staffId, $hourlyRate, $hoursWorked, $totalIncome, $payReccurance);

        if ($stmt->execute() === false) {
            die("Execute failed: " . $stmt->error);
        }

        
        header("Location: income_history.php");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Hours Worked</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>

    <h3>Enter Hours Worked</h3>
    <form action="" method="post">
        <label for="hours_worked">Hours Worked:</label>
        <input type="number" id="hours_worked" name="hours_worked" required min="1">
        <button type="submit">Submit</button>
    </form>

    <p><a href="logout.php">Logout</a></p>
</body>
</html>
