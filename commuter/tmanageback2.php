<?php
include('../db/tdbconn.php');

session_start(); // Start the session to access $_SESSION variables
$commuterID = $_SESSION["commuterid"];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];

    // Hash the current password input using SHA-512
    $hashedCurrentPassword = hash('sha512', $currentPassword);

    // Validate current password
    $sql = "SELECT password FROM commuter WHERE commuterid = $commuterID"; // Assuming your commuter table has a column named commuterid

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['password'];
        if ($hashedCurrentPassword === $storedPassword) {
            // Hash the new password using SHA-512
            $hashedNewPassword = hash('sha512', $newPassword);

            // Update the password
            $updateSql = "UPDATE commuter SET password = '$hashedNewPassword' WHERE commuterid = $commuterID";
            if ($conn->query($updateSql) === TRUE) {
                // Redirect to tmanage.php
                header("Location: tmanage.php");
                exit; // Ensure that subsequent code is not executed
            } else {
                // Redirect to tmanage.php with an error message
                header("Location: tmanage.php?error=" . urlencode("Error updating password: " . $conn->error));
                exit;
            }
        } else {
            // Redirect to tmanage.php with an error message
            header("Location: tmanage.php?error=" . urlencode("Current password is incorrect"));
            exit;
        }
    } else {
        // Redirect to tmanage.php with an error message
        header("Location: tmanage.php?error=" . urlencode("User not found"));
        exit;
    }
}

$conn->close();
?>
