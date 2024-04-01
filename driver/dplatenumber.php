<?php
include '../db/tdbconn.php'; // Include your database connection file

// Check if the driverid is set in the session
if(isset($_SESSION["driverid"])) {
    $driverid = $_SESSION["driverid"];
    
    // Prepare and execute a query to fetch the motorNumber based on the driverid
    $stmt = $conn->prepare("SELECT motorNumber FROM driver WHERE driverID = ?");
    $stmt->bind_param("s", $driverid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if the query was successful
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $motorNumber = $row['motorNumber'];

        // Prepare and execute a query to fetch the plateNumber based on the motorNumber
        $stmt2 = $conn->prepare("SELECT plateNumber FROM driverinfo WHERE motorNumber = ?");
        $stmt2->bind_param("s", $motorNumber);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        // Check if the query was successful
        if($result2->num_rows > 0) {
            $row2 = $result2->fetch_assoc();
            $plateNumber = $row2['plateNumber'];

            // Set the plateNumber as a session variable
            $_SESSION["plateNumber"] = $plateNumber;
        }
        $stmt2->close(); // Close the statement
    }
}

// Close the database connection
$stmt->close();
$conn->close();
?>
