<?php
include('../php/session_driver.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver | Dashboard</title>
    <link rel="icon" href="../img/logo3.png" type="image/png" />
    <link rel="stylesheet" href="../css/ddriver.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/965a209c77.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
    .loc{
        color: white;
    }
</style>
</head>

<body>
    <?php
    include 'dnav.php';
    ?>
    <?php include 'dplatenumber.php'; ?>
    <?php include 'dmotornumber.php'; ?>
    <!-- <h1>plateNumber:
        
    </h1>
    <h1>motorNummber:
        
    </h1>
    <h1>bookID:
        
    </h1> -->
    <div class="panel">
    <div class="address">
            <p class="loc"><i class="fa-solid fa-location-dot fa-lg" style="color: #ffff;"></i> Locating your current
                address...</p>
        </div>
        <!-- <p class="welcome">Welcome Back</p> -->
    </div>
    

    <p class="reminder">Are you well-rested and ready for your next ride? Remember, safe driving starts with you!</p>

    <div class="dashboard">
        <p class="categories">Services Categories</p>
    </div>

    <div class="buttons">
        <div class="book">
            <button class="booking" onclick="window.location.href='dearnings.php';">
                <i class="fa-solid fa-chart-line fa-2xl" style="color: #03b14e;"></i>
            </button>
            <p class="booklabel">Earnings</p>
        </div>

        <!-- <div class="scan">
            <button class="scanqr">
                <i class="fa-solid fa-comments fa-2xl" style="color: #03b14e;"></i>
            </button>
            <p class="scanlabel">Feedback</p>
        </div> -->
    </div>

    <div class="dashboard">
        <p class="categories">More</p>
    </div>

    <div class="buttons">
        <div class="book">
            <button class="booking" onclick="window.location.href='dmylocation.php';">
                <i class="fa-solid fa-location-crosshairs fa-2xl" style="color: #03b14e;"></i>
            </button>
            <p class="booklabel">My Location</p>
        </div>

        <div class="scan">
            <button class="scanqr" onclick="window.location.href='dhistory.php';">
                <i class="fa-solid fa-clock-rotate-left fa-2xl" style="color: #03b14e;"></i>
            </button>
            <p class="scanlabel">History</p>
        </div>
    </div>

    <div class="footer">
        <p class="foot">© TriSakay 2023 - 2024</p>
    </div>

    <script>
        function getLocation() {
            if ('geolocation' in navigator) {
                navigator.geolocation.getCurrentPosition(
                    ({
                        coords: {
                            latitude: userLat,
                            longitude: userLng
                        }
                    }) => {
                        console.log(`Latitude: ${userLat}, Longitude: ${userLng}`);

                        $.ajax({
                            url: 'https://nominatim.openstreetmap.org/reverse',
                            method: 'GET',
                            dataType: 'json',
                            data: {
                                format: 'json',
                                lat: userLat,
                                lon: userLng,
                                zoom: 18,
                            },
                            success: function(data) {
                                var address = data.display_name;
                                var addressParts = address.split(',');
                                var firstThreeParts = addressParts.slice(0, 3);
                                var shortenedAddress = firstThreeParts.join(',');

                                var iconHTML = '<i class="fa-solid fa-location-dot fa-lg" style="color: #ffff;"></i> ';
                                $('.address p').html(iconHTML + shortenedAddress);

                            },
                            error: function(error) {
                                console.error('Error getting address: ' + error);
                            }
                        });
                    },
                    (error) => {
                        console.error(`Error getting User's location: ${error.message}`);
                    }
                );
            }
        }

        getLocation();

        setInterval(getLocation, 2000);

        function checkBookingStatus() {
            // Make an AJAX request to check the bodynumber
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "driverback.php", true);

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText;

                    if (response === "notnull") {
                        // Redirect the user to found.php
                        window.location.href = "dontheway.php";
                    }
                }
            };

            xhr.send();
        }

        // Call the function initially
        setInterval(checkBookingStatus, 1000);
    </script>
</body>

</html>