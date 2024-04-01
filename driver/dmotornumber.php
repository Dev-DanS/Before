<?php
include '../db/tdbconn.php'; // Include your database connection file

// Check if the driverid is set in the session
if(isset($_SESSION["driverid"])) {
    $driverid = $_SESSION["driverid"];
    
    // Prepare and execute a query to fetch the plate number based on the driverid
    $stmt = $conn->prepare("SELECT motorNumber FROM driver WHERE driverID = ?");
    $stmt->bind_param("s", $driverid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if the query was successful
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $motorNumber = $row['motorNumber'];
        
        // Store the plate number in the session
        $_SESSION["motorNumber"] = $motorNumber;
    }
}

// Close the database connection
$stmt->close();
$conn->close();
?>
