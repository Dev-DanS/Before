<?php
session_start();

if ($_SESSION["role"] !== "driver") {
    header("Location: ../index.php");
    exit; 
}
?>