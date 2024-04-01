<?php
include('../db/tdbconn.php');



// Start a transaction
$conn->begin_transaction();

try {

    // Retrieve the latest booking details
    $selectSql = "SELECT plateNumber, bookCompletion, pickupAddress, dropoffAddress, fare, bookid, commuterid
    FROM booking 
    WHERE status = 'cancelled'
    ORDER BY bookingDate DESC 
    LIMIT 1;
    ";
    $stmt = $conn->prepare($selectSql);
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
        $commuterid = $row['commuterid'];

        // Get the motor number from driverinfo using plateNumber
        $driverInfoSql = "SELECT profile, firstName, LastName FROM commuter WHERE commuterid = ?";
        $stmt = $conn->prepare($driverInfoSql);
        $stmt->bind_param("s", $commuterid);
        $stmt->execute();
        $result = $stmt->get_result();
        
        
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $profile = $row['profile'];
                $firstName = $row['firstName'];
                $LastName = $row['LastName'];

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
