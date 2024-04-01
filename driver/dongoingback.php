<?php
include('../db/tdbconn.php');

$driverID = $_SESSION["driverid"];
$plateNumber = $_SESSION["plateNumber"];

// Start a transaction
$conn->begin_transaction();

try {
    $selectSql = "SELECT commuterID, dropoffPoint, dropoffaddress, fare, passengerCount
    FROM booking
    WHERE plateNumber = '$plateNumber'
    AND status = 'ongoing'
    ORDER BY bookingDate DESC
    LIMIT 1";
    $stmt = $conn->prepare($selectSql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $commuterID = $row['commuterID'];
        $dropoffPoint = $row['dropoffPoint'];
        $fare = $row['fare'];
        $passengerCount = $row['passengerCount'];
        $dropoffaddress = $row['dropoffaddress'];

        // Select the commuter's first name and last name using the commuterID
        $selectCommuteSql = "SELECT firstname, lastname
                             FROM commuter
                             WHERE commuterID = ?";
        $stmtCommute = $conn->prepare($selectCommuteSql);
        $stmtCommute->bind_param("i", $commuterID); // Assuming commuterID is an integer
        $stmtCommute->execute();
        $resultCommute = $stmtCommute->get_result();

        if ($resultCommute->num_rows > 0) {
            $rowCommute = $resultCommute->fetch_assoc();
            $firstname = $rowCommute['firstname'];
            $lastname = $rowCommute['lastname'];
        }

        $stmtCommute->close();
    }

    $stmt->close();
    $conn->commit();

} catch (Exception $e) {
    $conn->rollback(); 
    echo "Error: " . $e->getMessage();
}

$conn->close();

?>
