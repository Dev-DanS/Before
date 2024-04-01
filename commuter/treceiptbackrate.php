<?php
include('../db/tdbconn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $commuterID = $_SESSION["commuterid"];
    
    // Assuming 'rating' is the parameter sent via AJAX
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;

    if ($rating !== null) {
        $stmt = $conn->prepare("UPDATE booking 
                                SET rating = ?
                                WHERE commuterID = ? 
                                ORDER BY bookingDate DESC 
                                LIMIT 1");
        $stmt->bind_param("ii", $rating, $commuterID);
        $stmt->execute();
        $stmt->close();
        echo 'Rating inserted successfully!';
    } else {
        http_response_code(400);
        echo 'Invalid rating value';
    }
} else {
    http_response_code(405);
    echo 'Method Not Allowed';
}
?>
