<?php
session_start(); // Start the session
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h1>BookID:
        <?php echo $_SESSION["bookid"]; ?>
    </h1>
    <h1>Role:
        <?php echo $_SESSION["role"]; ?>
    </h1>
    <h1>Commuter</h1>
</body>
</html>