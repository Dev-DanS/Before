<?php
include('../db/tdbconn.php');

$dispatcherID = $_SESSION["dispatcherid"];

$sql = "SELECT email, firstname, lastname, mobilenumber FROM dispatcher WHERE dispatcherid = $dispatcherID";
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
