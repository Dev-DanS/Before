<?php
session_start();

if ($_SESSION["role"] !== "commuter") {
    header("Location: ../index.php");
    exit; 
}

?>