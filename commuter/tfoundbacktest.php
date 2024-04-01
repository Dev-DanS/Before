<?php
session_start();
include('../db/tdbconn.php');
$commuterID = $_SESSION["commuterid"];
$bookid = $_SESSION["bookid"];

// Prepare response array
$response = array();

$conn->begin_transaction();

try {
    $selectSql = "SELECT pickupPoint, driverLoc
    FROM booking
    WHERE commuterID = ?
    AND bookid = ?
    AND status = 'accepted'
    ORDER BY bookingDate DESC
    LIMIT 1";
    $stmt = $conn->prepare($selectSql);
    $stmt->bind_param("ss", $commuterID, $bookid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Assign values to response array
        $response['success'] = true;
        $response['pickupPoint'] = $row['pickupPoint'];
        $response['driverLoc'] = $row['driverLoc'];
    } else {
        $response['success'] = false;
        $response['message'] = 'No accepted bookings found.';
    }

    $stmt->close();
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    $response['success'] = false;
    $response['message'] = 'Error: ' . $e->getMessage();
}

$conn->close();

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
