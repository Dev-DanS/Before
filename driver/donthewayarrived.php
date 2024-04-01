<?php
include('../db/tdbconn.php');
session_start();
$driverID = $_SESSION["driverid"];
$plateNumber = $_SESSION["plateNumber"];

if(isset($_POST['arrived'])){
    $conn->begin_transaction();

    try {
        $updateSql = "UPDATE booking
                      SET status2 = 'arrived'
                      WHERE plateNumber = ?
                      AND status = 'accepted'
                      ORDER BY bookingDate DESC
                      LIMIT 1";
        $stmtUpdate = $conn->prepare($updateSql);
        $stmtUpdate->bind_param("s", $plateNumber); 
        $stmtUpdate->execute();
        $stmtUpdate->close();

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback(); 
        echo "Error: " . $e->getMessage();
    }

    $conn->close();
}
?>
