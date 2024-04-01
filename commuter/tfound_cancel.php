<?php
session_start();
include('../db/tdbconn.php'); // Include your database connection file

// Retrieve the bookid from the session
$bookid = $_SESSION["bookid"];
date_default_timezone_set('Asia/Manila');

// Get the current date and time in the Philippines
$currentDateTime = date('Y-m-d H:i:s');

// Prepare and execute a query to update the status to 'cancelled' and status2 to 'found_commuter'
$query = "UPDATE booking SET status = 'cancelled', status2 = 'found_commuter', bookCompletion = '$currentDateTime' WHERE bookid = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $bookid);
$stmt->execute();

// Check if the update was successful
if ($stmt->affected_rows > 0) {
    $_SESSION["bookid"] = null;
    echo "Booking cancelled successfully.";
} else {
    echo "Failed to cancel booking.";
}
?>
