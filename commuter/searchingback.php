<?php
session_start();
include_once("../db/tdbconn.php");

$commuterid = $_SESSION["commuterid"];
$bookid = $_SESSION["bookid"];

if ($conn === false) {
    die("Database connection failed.");
}

$query = "SELECT plateNumber FROM booking WHERE commuterID = ? AND bookid = ? AND status = 'accepted'";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $commuterid, $bookid);
$stmt->execute();
$stmt->bind_result($plateNumber);

if ($stmt->fetch()) {
    if ($plateNumber !== null) {
        echo "notnull";
    }
} else {
    echo "No pending booking found for the user.";
}

$stmt->close();
$conn->close();
?>
