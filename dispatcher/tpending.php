<?php
include('../php/session_dispatcher.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispatcher | Pending</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/965a209c77.js" crossorigin="anonymous"></script>
    <!-- <link rel="stylesheet" href="pending2.css"> -->
    <style>
        body {
            background-color: #03b14e !important;
        }

        .history {
            display: flex;
            justify-content: center;
            margin-top: 15px;
        }

        .historyData {
            padding-top: 10px;
            background-color: white;
            width: 90%;
            border-radius: 10px;
        }

        .line {
            width: 1px;
            height: 20px;
            background-color: #ccc;
            margin: 0 7px;
        }

        .locInfo {
            padding-left: 10%;
            font-size: small;
            color: gray;
        }

        .pickUp {
            margin-bottom: 0;
        }

        .infoText {
            margin-left: 10px;
            /* Adjust as needed */
            display: flex;
            /* Added */
            justify-content: space-between;
            /* Added */
            width: 100%;
            padding-right: 30px;
        }

        .bookingLabel,
        .bookingId {
            margin-bottom: 0;
            text-align: end;
            padding-right: 30px;
        }

        .plateNumber {
            color: black;
            font-weight: 400;
        }

        .date {
            padding-left: 10px;
        }

        .driverName{
            color: black;
            font-weight: 500;
        }
    </style>

</head>

<body>
    <?php
    include('disnav.php');
    ?>
    <div class="loading">
        <div id="booking-data">
            <?php include('bookingdata.php'); ?>
        </div>
    </div>

</body>

</html>