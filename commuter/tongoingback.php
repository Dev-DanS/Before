<?php
include('../db/tdbconn.php');
$commuterID = $_SESSION["commuterid"];
$bookid = $_SESSION["bookid"];

$conn->begin_transaction();

try {
    $selectSql = "SELECT plateNumber, dropoffPoint, dropoffaddress, fare, passengerCount
    FROM booking
    WHERE commuterID = '$commuterID'
    AND bookid = '$bookid'
    AND status = 'ongoing'
    ORDER BY bookingDate DESC
    LIMIT 1";
    $stmt = $conn->prepare($selectSql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $plateNumber = $row['plateNumber'];
        $dropoffPoint = $row['dropoffPoint'];
        $fare = $row['fare'];
        $passengerCount = $row['passengerCount'];
        $dropoffaddress = $row['dropoffaddress'];

        // Get motorNumber from driverinfo
        $selectMotorNumberSql = "SELECT motorNumber
                                 FROM driverinfo
                                 WHERE plateNumber = ?";
        $stmtMotorNumber = $conn->prepare($selectMotorNumberSql);
        $stmtMotorNumber->bind_param("s", $plateNumber);
        $stmtMotorNumber->execute();
        $resultMotorNumber = $stmtMotorNumber->get_result();

        if ($resultMotorNumber->num_rows > 0) {
            $rowMotorNumber = $resultMotorNumber->fetch_assoc();
            $motorNumber = $rowMotorNumber['motorNumber'];

            // Get firstname and lastname from driver using motorNumber
            $selectDriverSql = "SELECT firstName, lastName, profile
                                FROM driver
                                WHERE motorNumber = ?";
            $stmtDriver = $conn->prepare($selectDriverSql);
            $stmtDriver->bind_param("s", $motorNumber);
            $stmtDriver->execute();
            $resultDriver = $stmtDriver->get_result();

            if ($resultDriver->num_rows > 0) {
                $rowDriver = $resultDriver->fetch_assoc();
                $firstName = $rowDriver['firstName'];
                $lastName = $rowDriver['lastName'];
                $profile = $rowDriver['profile']; // Assuming 'profile' is the column name for the blob field
            }

            $stmtDriver->close();
        }

        $stmtMotorNumber->close();

        // Select the average rating
        $selectRatingSql = "SELECT ROUND(AVG(CASE WHEN rating > 0 THEN rating ELSE NULL END), 1) AS averageRating
                            FROM booking
                            WHERE plateNumber = ?
                            AND status = 'completed'";
        $stmtRating = $conn->prepare($selectRatingSql);
        $stmtRating->bind_param("s", $plateNumber);
        $stmtRating->execute();
        $resultRating = $stmtRating->get_result();

        if ($resultRating->num_rows > 0) {
            $rowRating = $resultRating->fetch_assoc();
            $averageRating = $rowRating['averageRating'];
        }

        $stmtRating->close();
    }

    $stmt->close();
    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();

