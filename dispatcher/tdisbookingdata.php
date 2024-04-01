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
    header('Location: tdisaccepted.php');
    exit; // Stop further execution of this script
}

// Query to select data from the "booking" table and join with the "commuters" table
$sql = "SELECT b.bookingid, b.bookid, b.toda, b.commuterid, b.pickupAddress, b.dropoffAddress, c.firstname, c.lastname, b.passengercount, b.fare, b.Distance
FROM booking b
LEFT JOIN commuter c ON b.commuterid = c.commuterid
WHERE b.status = 'pending';";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data from each row
    while ($row = $result->fetch_assoc()) {
        $commuterName = $row['firstname'] . ' ' . $row['lastname'];

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

                <div class="infoText">
                    <p class="driverName">' . $commuterName . ' •  Passenger: ' . $row["passengercount"] . '</p>

                    <p class="plateNumber"><strong>₱ ' . $row['fare'] . '</strong></p>
                </div>

                <div class="confirm">
                <form action="" method="GET">
                    <input type="hidden" name="bookingid" value="' . $row["bookingid"] . '">
                    <button type="submit" name="accept" class="confirm-btn" id="next">
                        Accept
                    </button>
                </form>
                </div>
            </div>
        </div>
    </div>';
    }
} else {
    echo "<h5 class = 'none'>There are currently no bookings to display.</h5>";
}

// Close the database connection
$conn->close();
