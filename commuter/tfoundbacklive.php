<?php
include('../db/tdbconn.php');
$commuterID = $_SESSION["commuterid"];
$bookid = $_SESSION["bookid"];

$conn->begin_transaction();

try {
    $selectSql = "SELECT pickupPoint, driverLoc
    FROM booking
    WHERE commuterID = ?
    AND status = 'accepted'
    ORDER BY bookingDate DESC
    LIMIT 1";
    $stmt = $conn->prepare($selectSql);
    $stmt->bind_param("s", $commuterID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pickupPoint = $row['pickupPoint'];
        $driverLoc = $row['driverLoc'];

        // Process the pickupPoint and driverLoc here
    }

    $stmt->close();
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
