<?php
include('../db/tdbconn.php');

$commuterid = 1;

$query = "SELECT plateNumber FROM booking WHERE commuterID = $commuterid AND status = 'accepted' ORDER BY bookingDate DESC LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $plateNumber = $row['plateNumber'];

    // Calculate the average rating
    $ratingQuery = "SELECT AVG(rating) AS avgRating FROM booking WHERE plateNumber = '$plateNumber' AND status = 'completed'";
    $ratingResult = mysqli_query($conn, $ratingQuery);

    if ($ratingResult && mysqli_num_rows($ratingResult) > 0) {
        $ratingRow = mysqli_fetch_assoc($ratingResult);
        $avgRating = $ratingRow['avgRating'];
    } else {
        $avgRating = 0; // If there are no completed bookings, set the average rating to 0.
    }
}

$query = "SELECT firstName, lastName, toda, bodyNumber FROM driver WHERE plateNumber = '$plateNumber'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $firstName = $row['firstName'];
    $lastName = $row['lastName'];
    $toda = $row['toda'];
    $bodyNumber = $row['bodyNumber'];
}
?>