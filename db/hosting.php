<?php
$servername = "localhost";
$username = "u200997458_Admin";
$password = "Mn5[R]=>!";
$database = "u200997458_TriSakay";

// Create a connection to the database
$conn = mysqli_connect($servername, $username, $password, $database);

// Check if the connection was successful
if ($conn === false) {
    die("Connection failed: " . mysqli_connect_error());
}

// echo "Connected successfully";

?>