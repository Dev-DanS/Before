<?php
include('../php/session_dispatcher.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispatcher | Dashboard</title>
    <link rel="icon" href="../img/logo3.png" type="image/png" />
    <link rel="stylesheet" href="../css/disdispatcher.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet" />
    <script src="https://kit.fontawesome.com/965a209c77.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php
    include 'disnav.php';
    ?>
    <div class="panel">
        <p class="location">
            <i class="fa-solid fa-location-dot fa-lg" style="color: #ffffff;"></i>
            <?php
include '../db/tdbconn.php';

// Assume $_SESSION["dispatcherid"] contains the dispatcher ID

$dispatcherID = $_SESSION["dispatcherid"];

// Assuming your table name is 'dispatcher' and the date column is 'toda'
$sql = "SELECT toda FROM dispatcher WHERE dispatcherid = $dispatcherID";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        $_SESSION['toda'] = $row["toda"];
        echo "" . $row["toda"] . " TODA<br> ";
    }
} else {
    echo "0 results";
}

mysqli_close($conn);
?>

        </p>
    </div>
    

    <p class="reminder">Reminder: Take a quick break if needed! Keeping yourself well-rested and focused ensures smooth operations.</p>

    <div class="dashboard">
        <p class="categories">Services Categories</p>
    </div>

    <div class="buttons">
        <div class="book">
            <button class="booking" onclick="window.location.href='tdispending.php';">
                <i class="fa-solid fa-list fa-2xl" style="color: #03b14e;"></i>
            </button>
            <p class="booklabel">Pending</p>
        </div>

    </div>

    <div class="dashboard">
        <p class="categories">More</p>
    </div>

    <div class="buttons">

        <div class="scan">
            <button class="scanqr" onclick="window.location.href='tdishistory.php';">
                <i class="fa-solid fa-clock-rotate-left fa-2xl" style="color: #03b14e;"></i>
            </button>
            <p class="scanlabel">History</p>
        </div>
    </div>

    <div class="footer">
        <p class="foot">© TriSakay 2023 - 2024</p>
    </div>
</body>
</html>
