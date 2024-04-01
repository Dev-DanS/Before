<?php
include('../php/session_driver.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver | On the way</title>
    <link rel="icon" href="../img/logo3.png" type="image/png" />
    <link rel="stylesheet" href="../css/tdontheway.css">
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
    include 'dnav.php';
    ?>
    <?php
    include 'donthewayback.php';
    ?>
    <div id="map"></div>
    <!-- <h5 id="accuracy"></h5>
    <h5 id="pickupPoint"></h5> -->

    <div class="rideData">
        <div class="rideText">
            <p class="otw">Estimated Time of Arrival</p>
            <p class="time" id="time">Calculating...</p>
        </div>
    </div>
    <div class="rideInfo">
        <div class="driverInfo">
            <img src="../img/male.png" alt="" style="width:50px; height: 50px; border-radius: 50%;">

            <div class="infoText">
                <p class="driverName"><?php echo $firstname; ?> <?php echo $lastname; ?> • Passengers: <?php echo $passengerCount; ?></p>
                <p class="plateNumber"><strong>₱ <?php echo $fare; ?></strong></p>
            </div>
        </div>
    </div>


    <div class="cancel">
        <button type="submit" class="cancel-btn" id="generateData">
            Cancel Ride
        </button>
    </div>

    <script>
        const map = L.map('map', {
            zoomControl: false,
            doubleClickZoom: false
        }).setView([0, 0], 15);

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


        let pickupPointStr = '<?php echo $pickupPoint; ?>';
        let [lat, lng] = pickupPointStr.split(',').map(Number);

        let pickupPoint = {
            lat: lat,
            lng: lng
        };

        console.log(pickupPoint);
        // let dropoffPoint = { lat: 14.969750598785996, lng: 120.92664291012869 };
        let dropoffPoint = {
            lat: 14.958627583238098,
            lng: 120.89392202853571
        };
        let driverLoc;
        // const dropoffMarker = L.marker([dropoffPoint.lat, dropoffPoint.lng]).addTo(map);
        const pickupPointfMarker = L.marker([pickupPoint.lat, pickupPoint.lng], {
            icon: redMarkerIcon
        }).addTo(map);

        const marker = L.marker([0, 0], {
            icon: greenMarkerIcon
        }).addTo(map);
        let accuracyCircle;

        let intervalId;

        let distance;
        let durationMinutes;

        function updateLocation(position) {
            const {
                latitude,
                longitude,
                accuracy
            } = position.coords;
            driverLoc = {
                lat: latitude,
                lng: longitude
            };

            console.log('Current ', driverLoc);

            let driverlive = `${latitude},${longitude}`;

            console.log('Driver live: ', driverlive);

            marker.setLatLng([latitude, longitude]);

            const distanceToPickup = map.distance([latitude, longitude], [pickupPoint.lat, pickupPoint.lng]);

            $.ajax({
                url: 'donthewaylive.php',
                type: 'POST',
                data: {
                    driverLoc: driverlive
                },
                success: function(response) {
                    console.log('Coordinates saved to database.');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                }
            });

            if (distanceToPickup <= 20) {
                // Send AJAX request
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'donthewayarrived.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        console.log('Arrived successfully');
                    } else {
                        console.error('Error:', xhr.statusText);
                    }
                };
                xhr.onerror = function() {
                    console.error('Request failed');
                };
                xhr.send('arrived=true');
            }

            // if (!accuracyCircle) {
            //     accuracyCircle = L.circle([latitude, longitude], {
            //         radius: accuracy,
            //         color: 'blue',
            //         opacity: 0.5,
            //         fillOpacity: 0.1
            //     }).addTo(map);
            // } else {
            //     accuracyCircle.setLatLng([latitude, longitude]).setRadius(accuracy);
            // }

            map.setView([latitude, longitude], 17);

            // document.getElementById('accuracy').innerText = `Accuracy: ${accuracy} meters`;
            Routing(driverLoc);

        }

        let routeLayer;

        function Routing(driverLoc) {
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
                    document.getElementById('time').innerText = `${durationMinutes} min(s)`;

                })
                .catch(error => {
                    console.error('Error fetching route from OSRM:', error);
                });
        }



        function handleError(error) {
            console.error('Error getting location:', error);
        }

        const options = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0,
        };

        function startTracking() {
            const watchId = navigator.geolocation.watchPosition(updateLocation, handleError, options);

            window.addEventListener('beforeunload', () => {
                navigator.geolocation.clearWatch(watchId);
            });
        }

        function checkBookingStatus() {
            // Make an AJAX request to check the bodynumber
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "donthewaycheck.php", true);

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = xhr.responseText;

                    if (response === "notnull") {
                        // Redirect the user to found.php
                        window.location.href = "dongoing.php";
                    }
                }
            };

            xhr.send();
        }

        // Call the function initially
        setInterval(checkBookingStatus, 1000);

        startTracking();

        $(document).ready(function() {
            $("#generateData").click(function() {
                if (confirm("Are you sure you want to cancel this booking?")) {
                    $.ajax({
                        url: "ontheway_cancel.php",
                        method: "POST",
                        success: function(response) {
                            alert(response);
                            window.location.href = "dreceipt_cancel.php";
                        }
                    });
                }
            });
        });

        function checkStatus() {
            $.ajax({
                url: "search_check.php",
                method: "GET",
                success: function (response) {
                    if (response === "cancelled") {
                        window.location.href = "dreceipt_cancel.php";
                    }
                }
            });
        }

        checkStatus();
        setInterval(checkStatus, 2000);
    </script>



</body>

</html>