<?php
session_start();

if ($_SESSION["role"] !== "dispatcher") {
    header("Location: ../index.php");
    exit; 
}
?>