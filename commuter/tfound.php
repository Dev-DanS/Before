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
    <link rel="stylesheet" href="../css/tcfound.css">
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
    <?php
    include 'tfoundback.php';
    ?>

    <div id="map" style="width: 100%; height: 65vh;"></div>

    <div class="rideData">
        <div class="rideText">
            <p class="otw">Driver is on the way</p>
            <p class="time" id="time">Loading...</p>
        </div>
    </div>
    <div class="rideInfo">
        <div class="driverInfo">
            <?php echo '<img src="data:image/jpeg;base64,' . base64_encode($profile) . '" alt="" style="width:50px; height: 50px; border-radius: 50%;">'; ?>

            <div class="infoText">
                <p class="driverName"><?php echo $firstName; ?> <?php echo $lastName; ?> • <?php echo $averageRating; ?> <i class="fa-solid fa-star fa-sm" data-value="1" style="color: #FFFF00;"></i></p>
                <p class="plateNumber"><?php echo $plateNumber; ?></p>
            </div>
        </div>
    </div>


    <div class="cancel">
        <button type="submit" class="cancel-btn" id="generateData">
            Cancel Booking
        </button>
    </div>

    <div class="start">
        <button type="submit" class="start-btn" id="start">
            Start Ride
        </button>
    </div>

    <script>
        var map = L.map('map', {
            zoomControl: false,
            doubleClickZoom: false
        }).setView([14.954264838385502, 120.90079147651407], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const createMarkerIcon = (color) => L.icon({
            iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        const blueIcon = createMarkerIcon('blue');
        const greenMarkerIcon = createMarkerIcon('green');
        const redMarkerIcon = createMarkerIcon('red');

        let pickupPoint;
        let driverLoc;


        function fetchBookingDetails() {
            // AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'tfoundbacktest.php', true);
            xhr.onload = function() {
                if (xhr.status == 200) {
                    // Process the response
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Log values to console
                        pickupPoint = response.pickupPoint;
                        driverLoc = response.driverLoc;
                        console.log('Pickup Point:', pickupPoint);
                        console.log('Driver Location:', driverLoc);
                        createMarker(pickupPoint, 'pickup');
                        createMarker(driverLoc, 'driver');
                        Routing(driverLocMarker.getLatLng(), pickupPointMarker.getLatLng());

                    } else {
                        console.error('Error: ' + response.message);
                    }
                } else {
                    console.error('Request failed. Status: ' + xhr.status);
                }
            };
            xhr.send();
        }

        let pickupPointMarker = null;
        let driverLocMarker = null;

        function createMarker(location, markerType) {
            let marker = null;

            if (markerType === 'pickup') {
                marker = pickupPointMarker;
            } else if (markerType === 'driver') {
                marker = driverLocMarker;
            }

            if (marker) {
                map.removeLayer(marker);
            }

            let [lat, lng] = location.split(',').map(parseFloat);

            if (markerType === 'pickup') {
                pickupPointMarker = L.marker([lat, lng], {
                    icon: greenMarkerIcon
                }).addTo(map);
            } else if (markerType === 'driver') {
                driverLocMarker = L.marker([lat, lng], {
                    icon: redMarkerIcon
                }).addTo(map);
            }
        }

        let routeLayer;

        function Routing(driverLoc, pickupPoint) {
            const url = `https://router.project-osrm.org/route/v1/driving/${driverLoc.lng},${driverLoc.lat};${pickupPoint.lng},${pickupPoint.lat}?overview=full&geometries=geojson`;

            axios.get(url)
                .then(response => {
                    distance = (response.data.routes[0].distance / 1000).toFixed(2);
                    const averageSpeed = 15;
                    const durationHours = distance / averageSpeed;
                    durationMinutes = Math.round(durationHours * 60);

                    const routeCoordinates = response.data.routes[0].geometry.coordinates;
                    const geojsonRoute = {
                        type: "Feature",
                        properties: {},
                        geometry: {
                            type: "LineString",
                            coordinates: routeCoordinates
                        }
                    };

                    if (routeLayer) map.removeLayer(routeLayer);

                    routeLayer = L.geoJSON(geojsonRoute, {
                        style: {
                            weight: 3,
                            color: '#03b14e'
                        }
                    }).addTo(map);

                    const boundsWithPadding = routeLayer.getBounds().pad(0.1);
                    map.fitBounds(routeLayer.getBounds().pad(0.1));
                    document.getElementById('time').innerText = `${durationMinutes} min(s)`;

                })
                .catch(error => {
                    console.error('Error fetching route from OSRM:', error);
                });
        }

        function updateButtonVisibility() {
            if (pickupPointMarker && driverLocMarker) {
                const distance = pickupPointMarker.getLatLng().distanceTo(driverLocMarker.getLatLng());
                if (distance <= 20) {
                    document.querySelector('.start-btn').style.display = 'block';
                    document.querySelector('.cancel-btn').style.display = 'none';
                } else {
                    document.querySelector('.start-btn').style.display = 'none';
                    document.querySelector('.cancel-btn').style.display = 'block';
                }
            }
        }

        document.getElementById('start').addEventListener('click', function() {
            // Redirect to hello.html
            window.location.href = 'tfoundbackstart.php';
        });

        setInterval(updateButtonVisibility, 1000);



        setInterval(fetchBookingDetails, 1000);

        $(document).ready(function() {
            $("#generateData").click(function() {
                if (confirm("Are you sure you want to cancel this booking?")) {
                    $.ajax({
                        url: "tfound_cancel.php",
                        method: "POST",
                        success: function(response) {
                            alert(response);
                            window.location.href = "treceipt_cancel.php";
                        }
                    });
                }
            });
        });

        function checkStatus() {
            $.ajax({
                url: "book_status.php",
                method: "POST",
                success: function (response) {
                    if (response === 'pending') {
                        window.location.href = "newsearching.php";
                    } else if (response === 'ongoing') {
                        window.location.href = "tongoing.php";
                    } else if (response === 'cancelled') {
                        window.location.href = "treceipt_cancel.php";
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