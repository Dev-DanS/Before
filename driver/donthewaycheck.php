<?php
session_start();
include_once("../db/tdbconn.php");

$driverID = $_SESSION["driverid"];
$plateNumber = $_SESSION["plateNumber"];

if ($conn === false) {
    die("Database connection failed.");
}

$query = "SELECT plateNumber FROM booking WHERE plateNumber = ? AND status = 'ongoing'";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $plateNumber);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "notnull";
} else {
    echo "No completed booking found for the user.";
}

$stmt->close();
$conn->close();
?>
