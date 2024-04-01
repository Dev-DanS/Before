<?php
include '../db/tdbconn.php';
session_start();

$bookid = $_SESSION["bookid"];
$sql = "SELECT bookingdate FROM booking WHERE bookid = '$bookid'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$bookingDate = $row['bookingdate'];

// Set the timezone to Philippines
date_default_timezone_set('Asia/Manila');
$currentDate = time();
$dateAndTime = date("Y-m-d H:i:s", $currentDate); 

// Convert booking date and current date to timestamp
$bookingTimestamp = strtotime($bookingDate);
$currentTimestamp = strtotime($dateAndTime);

// Calculate remaining time in seconds
$remainingTime = $bookingTimestamp + 180 - $currentTimestamp;

if ($remainingTime > 0) {
    $remainingMinutes = floor($remainingTime / 60);
    $remainingSeconds = $remainingTime % 60;
    $remainingTimeFormatted = sprintf("%02d:%02d", $remainingMinutes, $remainingSeconds);
    echo json_encode(array("remainingTime" => $remainingTimeFormatted));
} else {
    echo json_encode(array("expired" => true));
}
?>
