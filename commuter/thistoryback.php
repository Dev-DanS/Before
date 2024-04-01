<?php
include('../db/tdbconn.php');
$commuterID = $_SESSION["commuterid"];

$query = "SELECT b.bookid, b.pickupAddress, b.dropoffAddress, b.bookCompletion, b.plateNumber, b.rating, b.fare, b.status, d.motorNumber
          FROM booking b
          JOIN driverinfo d ON b.plateNumber = d.plateNumber
          WHERE b.commuterID = '$commuterID' AND (b.status = 'completed' OR b.status = 'cancelled')
          ORDER BY b.bookCompletion DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Process the results, e.g., fetch and display them
while ($row = mysqli_fetch_assoc($result)) {
    // Get the driver's first and last name
    $query2 = "SELECT firstName, lastName
               FROM driver
               WHERE motorNumber = '" . $row['motorNumber'] . "'";
    $result2 = mysqli_query($conn, $query2);
    if (!$result2) {
        die("Query failed: " . mysqli_error($conn));
    }
    $driver = mysqli_fetch_assoc($result2);
    $driverName = $driver['firstName'] . ' ' . $driver['lastName'];

    // Generate star icons based on the rating
    $rating = intval($row['rating']);
    $stars = "";
    for ($i = 0; $i < $rating; $i++) {
        $stars .= '<i class="fa-solid fa-star fa-sm" style="color: #FFFF00;"></i>';
    }

    // Format the bookCompletion date
    $bookCompletionFormatted = date('j F Y, g:i A', strtotime($row['bookCompletion']));

    // Get the status
    $status = $row['status'];

    // Set the text color based on the status
    $statusColor = ($status === 'completed') ? 'green' : 'red';
    $statusText = ($status === 'completed') ? 'Completed' : 'Cancelled';

    // Output the HTML
    echo '
    <div class="history">
        <div class="historyData">
            <div class="locInfo">
                <div class="bookingData">
                    <p class="bookingLabel">BOOKING ID</p>
                    <p class="bookingId">' . $row['bookid'] . '</p>
                </div>
    
                <p class="pickUp"><i class="fa-solid fa-location-dot fa-xl" style="color: #03b14e;"></i>
                ' . $row['pickupAddress'] . '</p>
                <div class="line"></div>
                <p class="dropOff"><i class="fa-solid fa-location-dot fa-xl" style="color: #ff0000;"></i>
                ' . $row['dropoffAddress'] . '</p>
    
                <div class="date">
                    <p class="dateTime">' . $bookCompletionFormatted . ' <span style="color: ' . $statusColor . ';">' . $statusText . '</span></p>
                </div>
                <div class="infoText">
                    <p class="driverName">' . $driverName . ' • ' . $stars . '</p>
    
                    <p class="plateNumber"><strong>₱ ' . $row['fare'] . '</strong></p>
                </div>
            </div>
        </div>
    </div>';
}

mysqli_close($conn);

?>
