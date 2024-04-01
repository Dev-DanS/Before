<?php
include('../db/tdbconn.php');

$bookingid = $_SESSION["bookingid"];


$conn->begin_transaction();

$selectSql = "SELECT pickupAddress, dropoffAddress, fare, passengerCount, distance
    FROM booking 
    WHERE bookingid = ? AND status = 'pending'
    ORDER BY bookingDate DESC 
    LIMIT 1;
    ";

$stmt = $conn->prepare($selectSql);
$stmt->bind_param("i", $bookingid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
   
    $pickupAddress = $row['pickupAddress'];
    $dropoffAddress = $row['dropoffAddress'];
    $passengerCount = $row['passengerCount'];
    $distance = $row['distance'];
    $fare = $row['fare'];
}

$stmt->close();
$conn->commit();
$conn->close();
?>
