<?php
// Include the database connection file
include('../db/tdbconn.php');


// Check if the accept button is clicked and process accordingly
if (isset($_GET['accept'])) {
    // Get the bookingid from the form data
    $bookingid = $_GET['bookingid'];
    
    // Set the bookingid as a session variable
    $_SESSION['bookingid'] = $bookingid;
    
    // Redirect to accepted.php or perform other actions
    header('Location: accepted.php');
    exit; // Stop further execution of this script
}

// Query to select data from the "booking" table and join with the "commuters" table
$sql = "SELECT b.bookingID, b.toda, b.commuterid, c.firstname, c.lastname, b.pickupAddress, b.dropoffAddress, b.passengercount, b.fare, b.distance
FROM booking b
LEFT JOIN commuter c ON b.commuterid = c.commuterid
WHERE b.status = 'pending';";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data from each row
    // while ($row = $result->fetch_assoc()) {
    //     echo "<div class='booking-info'>";
    //     echo "<p>Booking ID: " . $row["bookingID"] . "</p>";
    //     echo "<p>Commuter: " . $row["firstname"] . "</p>";
    //     echo "<p>Passenger Count: " . $row["passengerCount"] . "</p>";
    //     echo "<p>Fare: ₱" . $row["fare"] . "</p>";
    //     echo "<p>Distance: " . number_format($row["distance"], 3) . " km</p>";
    //     echo "<a href='preview.php?bookingid=" . $row["bookingID"] . "'>Accept</a>";
    //     echo "</div>";
    // }

    while ($row = $result->fetch_assoc()) {
        echo '<div class="history">';
echo '<div class="historyData">';
echo '<div class="locInfo">';
echo '<div class="bookingData">';
echo '<p class="bookingLabel">BOOKING ID</p>';
echo '<p class="bookingId">' . $row["bookingID"] . '</p>';
echo '<div class="date">';
echo '<p class="dateTime">02/21/2024, 8:36 PM</p>';
echo '</div>';
echo '</div>';
echo '<p class="pickUp"><i class="fa-solid fa-location-dot fa-xl" style="color: #03b14e;"></i> ' . $row["pickupAddress"] . '</p>';
echo '<div class="line"></div>';
echo '<p class="dropOff"><i class="fa-solid fa-location-dot fa-xl" style="color: #ff0000;"></i> ' . $row["dropoffAddress"] . '</p>';
echo '<p class="distance">Distance: <span style="color: red;">' . $row["distance"] . ' KM</span></p>';
echo '<div class="infoText">';
echo '<p class="driverName">' . $row["firstname"] . ' ' . $row["lastname"] . '</p>';
echo '<p class="plateNumber"><strong>₱' . $row["fare"] . '</strong></p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

    }

} else {
    echo "No data found.";
}

// Close the database connection
$conn->close();
