<?php
include('../php/session_commuter.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commuter | Driver Found</title>
    <link rel="icon" href="../img/logo3.png" type="image/png" />
    <link rel="stylesheet" href="../css/cfound.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet" />
    <script src="https://kit.fontawesome.com/965a209c77.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
    <?php
    include 'foundback.php';
    ?>
    <?php
    include 'cnav.php';
    ?>
    <div class="loading">
        <h5><b>We've found you a driver, and they're on their way to pick you up.</b></h5>
        <hr>
        <p>Driver:
            <?php echo $firstName; ?>
            <?php echo $lastName; ?>
        </p>
        <p>Body Number:
            <?php echo $bodyNumber; ?>
        </p>
        <p>Toda:
            <?php echo $toda; ?>
        </p>
        <p>Plate Number:
            <?php echo $plateNumber; ?>
        </p>
        <p>Rating:
            <?php echo number_format($avgRating, 1); ?> <i class="fa-solid fa-star" style="color: #03b14e;"></i>
        </p>
        <div class="confirm">
            <button class="confirm-btn" id="confirm-btns">
                <i class="fa-solid fa-expand fa-lg" style="color: #fff;"></i> Scan QR
            </button>
        </div>
    </div>
    <script>
        document.getElementById("confirm-btns").addEventListener("click", function () {
            window.location.href = "scan.php";
        });
    </script>

</body>

</html>