<?php
session_start();
include('../db/tdbconn.php'); // Include your database connection file

// Retrieve the bookid from the session
$bookid = $_SESSION["bookid"];

// Prepare and execute a query to update the status to 'cancelled'
$query = "UPDATE booking SET status = 'cancelled' WHERE bookid = ?";
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

