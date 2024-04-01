<?php
include '../db/tdbconn.php';
// Check if the bookid session exists and is not empty
if(isset($_SESSION["bookid"]) && !empty($_SESSION["bookid"])) {
    $bookid = $_SESSION["bookid"];
    
    // Query the booking table
    $query = "SELECT * FROM booking WHERE bookid = '$bookid' AND (status = 'pending' OR status = 'accepted' OR status = 'ongoing')";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) > 0) {
        // Fetch the row
        $row = mysqli_fetch_assoc($result);

        // Check the status and redirect accordingly
        if($row['status'] == 'pending') {
            header("Location: searching.php");
            exit();
        } elseif($row['status'] == 'accepted') {
            header("Location: tfound.php");
            exit();
        } elseif($row['status'] == 'ongoing') {
            header("Location: tongoing.php");
            exit();
        }
    }
}

// If none of the conditions are met, do nothing
?>
