<?php
include('../db/tdbconn.php');

$commuterID = $_SESSION["commuterid"];
$bookid = $_SESSION["bookid"];

// Start a transaction
$conn->begin_transaction();

try {
    // Update the booking status
    $updateSql = "UPDATE booking 
                  SET status = 'completed' 
                  WHERE commuterID = ? AND bookid = ?
                  ORDER BY bookingDate DESC 
                  LIMIT 1";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("is", $commuterID, $bookid);
    $stmt->execute();
    $stmt->close();

    // Retrieve the latest booking details
    $selectSql = "SELECT plateNumber, bookCompletion, pickupAddress, dropoffAddress, fare, bookid 
    FROM booking 
    WHERE commuterID = ? AND bookid = ? AND status = 'completed'
    ORDER BY bookingDate DESC 
    LIMIT 1;
    ";
    $stmt = $conn->prepare($selectSql);
    $stmt->bind_param("is", $commuterID, $bookid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $plateNumber = $row['plateNumber'];
        $bookCompletion = $row['bookCompletion'];
        $formattedDate = date("j F Y, g:i A", strtotime($bookCompletion));
        $pickupAddress = $row['pickupAddress'];
        $dropoffAddress = $row['dropoffAddress'];
        $fare = $row['fare'];
        $bookid = $row['bookid'];

        // Get the motor number from driverinfo using plateNumber
        $driverInfoSql = "SELECT motornumber FROM driverinfo WHERE plateNumber = ?";
        $stmt = $conn->prepare($driverInfoSql);
        $stmt->bind_param("s", $plateNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $motorNumber = $row['motornumber'];

            // Get the profile from driver table using motorNumber
            $driverSql = "SELECT profile, firstName, LastName FROM driver WHERE motornumber = ?";
            $stmt = $conn->prepare($driverSql);
            $stmt->bind_param("s", $motorNumber);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $profile = $row['profile'];
                $firstName = $row['firstName'];
                $LastName = $row['LastName'];

            }
        }
    }

    $stmt->close();
    $conn->commit(); // Commit the transaction

    // Set the session variable to null
    $_SESSION["bookid"] = null;

} catch (Exception $e) {
    $conn->rollback(); // Rollback the transaction if an error occurs
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
