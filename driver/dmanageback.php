<?php
include('../db/tdbconn.php');

$driverID = $_SESSION["driverid"];

$sql = "SELECT email, firstname, lastname, mobilenumber FROM driver WHERE driverID = $driverID";
$result = mysqli_query($conn, $sql);

if ($result) {
    // Fetch the data from the result set
    $row = mysqli_fetch_assoc($result);
    $email = $row['email'];
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $mobilenumber = $row['mobilenumber'];

    // Use the retrieved data as needed
} else {
    // Handle the case where the query fails
}

// Don't forget to close the connection
mysqli_close($conn);
?>
