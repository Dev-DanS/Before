<?php
include('../php/session_commuter.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commuter | Searching</title>
    <link rel="icon" href="../img/logo3.png" type="image/png" />
    <link rel="stylesheet" href="../css/csearching.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/965a209c77.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
    <?php
    include 'cnav.php';
    ?>
    <div class="loading">
        <p><b>Searching for drivers...</b></p>
        <p id="wait-time">Estimated time: <span id="countdown">Loading...</span></p>
        <span class="load" id="spin"><i class="fa-solid fa-spinner fa-spin-pulse fa-xl" style="color: #000000;"></i></span>
        <!-- <button id="cancel-button" type="submit" class="btn btn-default custom-btn red-text">
            Cancel Booking
        </button>  -->
        <div class="confirm">
            <button class="confirm-btn" id="confirm-btns">
                Cancel Booking
            </button>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function updateCountdown() {
                $.ajax({
                    url: 'searching_testback.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.expired) {
                            $('#wait-time').html('No drivers are available in your area at this time.<br>Please try again later or cancel your request.');
                            $('#confirm-btns').css('display', 'block');
                            $('#spin').css('display', 'none');
                            $('#wait-time').css('color', 'red').css('font-weight', 'bold');
                        } else {
                            $('#countdown').text(data.remainingTime);
                        }
                    },
                    error: function() {
                        console.log('Error fetching remaining time.');
                    }
                });
            }

            // Update countdown every second
            setInterval(updateCountdown, 1000);
        });

        $(document).ready(function () {
            $("#confirm-btns").click(function () {
                $.ajax({
                    url: "search_cancel.php",
                    method: "POST",
                    success: function (response) {
                        alert(response);
                        window.location.href = "commuter.php";
                    }
                });
            });
        });

        function checkBookingStatus() {
            $.ajax({
                url: "search_check.php",
                method: "GET",
                success: function (response) {
                    if (response === "cancelled") {
                        window.location.href = "commuter.php";
                    }
                }
            });
        }

        checkBookingStatus();
        setInterval(checkBookingStatus, 2000);

        function checkBookingStatus() {
            // Make an AJAX request to check the bodynumber
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "searchingback.php", true);

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText;

                    if (response === "notnull") {
                        // Redirect the user to found.php
                        window.location.href = "tfound.php";
                    }
                }
            };

            xhr.send();
        }

        // Call the function initially
        checkBookingStatus();

        // Set up a recurring check every 3 seconds
        setInterval(checkBookingStatus, 2000);

        function checkStatus() {
            $.ajax({
                url: "book_status.php",
                method: "POST",
                success: function (response) {
                    if (response === 'accepted') {
                        window.location.href = "tfound.php";
                    } else if (response === 'ongoing') {
                        window.location.href = "tongoing.php";
                    } else {
                        setTimeout(checkStatus, 2000);
                    }
                }
            });
        }

        checkStatus();
    </script>

</body>

</html>