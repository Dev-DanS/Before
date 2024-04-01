<?php
include('../db/tdbconn.php');

    $bookingID = 1;

    $query = "SELECT pickupPoint, dropoffPoint, toda FROM booking WHERE bookingID = $bookingID";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pickupPoint = explode(',', $row['pickupPoint']);
        $dropoffPoint = explode(',', $row['dropoffPoint']);
        $toda = $row['toda'];

        $query = "SELECT Coordinates FROM todaterminal WHERE toda = '$toda'";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $terminal = json_decode($row['Coordinates'], true);

            $response = [
                'pickupPoint' => $pickupPoint,
                'dropoffPoint' => $dropoffPoint,
                'terminal' => $terminal,
            ];

            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'Terminal not found for the provided TODA']);
        }
    } else {
        echo json_encode(['error' => 'Booking not found']);
    }

?>