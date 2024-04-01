<?php
include('../db/tdbconn.php');


$bookid = $_SESSION["bookid"];

// Start a transaction
$conn->begin_transaction();

try {
    // Update the booking status
    $updateSql = "UPDATE booking 
                  SET status = 'completed' 
                  WHERE  bookid = ?
                  ORDER BY bookingDate DESC 
                  LIMIT 1";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("i", $bookid);
    $stmt->execute();
    $stmt->close();

    // Retrieve the latest booking details
    $selectSql = "SELECT plateNumber, bookCompletion, pickupAddress, dropoffAddress, fare, bookid, commuterid
    FROM booking 
    WHERE bookid = ? AND status = 'completed'
    ORDER BY bookingDate DESC 
    LIMIT 1;
    ";
    $stmt = $conn->prepare($selectSql);
    $stmt->bind_param("i", $bookid);
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
