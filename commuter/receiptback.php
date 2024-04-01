<?php


// Include the database connection
include('../db/tdbconn.php');

// Get the commuterid from the session
$commuterID = $_SESSION["commuterid"];

// Define the SQL query to update the "booking" table
$sql = "UPDATE booking 
        SET status = 'completed' 
        WHERE commuterID = ? 
        ORDER BY bookingDate DESC 
        LIMIT 1";

// Create a prepared statement
$stmt = $conn->prepare($sql);

// Bind the commuterid to the prepared statement
$stmt->bind_param("i", $commuterID);

// Execute the prepared statement
if ($stmt->execute()) {
} else {
    echo "Error updating booking status: " . $conn->error;
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();
?>

<?php
include '../db/tdbconn.php'; // Include your database connection script

$commuterID = $_SESSION["commuterid"];

// Your SQL query with the defined $commuterid
$query = "SELECT plateNumber, toda, pickupPoint, dropoffPoint, fare, distance FROM booking WHERE commuterID = $commuterID ORDER BY bookingDate DESC  LIMIT 1";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Fetch the data from the result set
$row = mysqli_fetch_assoc($result);

// Store the retrieved data in variables
$plateNumber = $row['plateNumber'];
$toda = $row['toda'];
$pickupPoint = $row['pickupPoint'];
$dropoffPoint = $row['dropoffPoint'];
$fare = $row['fare'];
$distance = $row['distance'];

?>