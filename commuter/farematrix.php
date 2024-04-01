<?php
include '../db/tdbconn.php';

// Assuming your connection is in $conn
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT baseFare, perKM, nightDiff, farePerPassenger 
          FROM farematrix 
          WHERE status = 'active' 
          ORDER BY DateCreated DESC 
          LIMIT 1";

$result = mysqli_query($conn, $query);

$response = array();

if ($result) {
    // Fetch the data from the result set
    $row = mysqli_fetch_assoc($result);

    // Add the data to the response array
    $response['baseFare'] = $row['baseFare'];
    $response['perKM'] = $row['perKM'];
    $response['nightDiff'] = $row['nightDiff'];
    $response['farePerPassenger'] = $row['farePerPassenger'];
} else {
    // If there's an error, include an error message in the response
    $response['error'] = "Error: " . mysqli_error($conn);
}

// Close the connection
mysqli_close($conn);

// Return the response as JSON
echo json_encode($response);
?>