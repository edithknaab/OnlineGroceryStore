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


$order_id = $_POST['order_id'];
$new_quantity = $_POST['quantity'];


$updateQuery = "UPDATE order_history SET quantity = $new_quantity WHERE order_id = $order_id";

if ($conn->query($updateQuery) === TRUE) {
    
    $response = [
        'orderId' => $order_id,
        'quantity' => $new_quantity,
        'price' => 123.45, 
        'totalPrice' => 678.90 
    ];

    echo json_encode($response);
} else {
   
    $response = [
        'error' => 'Failed to update quantity'
    ];

    echo json_encode($response);
}

$conn->close();
?>
