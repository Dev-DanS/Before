<?php
session_start(); 

include('../db/tdbconn.php');

if (isset($_GET['accept'])) {
    $bookingid = $_GET['bookingid'];
    
    $_SESSION['bookingid'] = $bookingid;
    
    header('Location: accepted.php');
    exit; 
}

$sql = "SELECT b.bookingid, b.toda, b.commuterid, c.firstname, b.passengercount, b.fare, b.conveniencefee, b.Distance
FROM booking b
LEFT JOIN commuter c ON b.commuterid = c.commuterid
WHERE b.status = 'pending';";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='booking-info'>";
        echo "<p>Booking ID: " . $row["bookingid"] . "</p>";
        echo "<p>Commuter: " . $row["firstname"] . "</p>";
        echo "<p>Passenger Count: " . $row["passengercount"] . "</p>";
        echo "<p>Fare: ₱" . $row["fare"] . "</p>";
        echo "<p>Convenience Fee: ₱" . $row["conveniencefee"] . "</p>";
        echo "<p>Distance: " . number_format($row["Distance"], 3) . " km</p>";

        echo "<form action='' method='GET'>";
        echo "<input type='hidden' name='bookingid' value='" . $row["bookingid"] . "'>";
        echo "<button type='submit' name='accept' class='btn btn-default custom-btn'>
            Accept
        </button>";
        echo "</form>";

        echo "</div>";
    }
} else {
    echo "No data found.";
}

// Close the database connection
$conn->close();
?>
