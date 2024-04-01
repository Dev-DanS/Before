<?php
include('../php/session_commuter.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commuter | En route</title>
    <link rel="icon" href="../img/logo3.png" type="image/png" />
    <link rel="stylesheet" href="../css/congoing.css">
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
    include 'cnav.php';
    ?>
    <div id="map" style="height: 400px;"></div>
    <br>
    <?php
    include('../db/tdbconn.php');
        $commuterID = 1;

        $sql = "SELECT pickupPoint, dropoffPoint, fare, distance FROM booking WHERE commuterID = ? AND status = 'accepted' ORDER BY bookingDate DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $commuterID);
        $stmt->execute();
        $stmt->bind_result($pickupPoint, $dropoffPoint, $fare, $distance);
        $stmt->fetch();
        $stmt->close();
        $pickupCoords = explode(',', $pickupPoint);
        $dropoffCoords = explode(',', $dropoffPoint);
    ?>

    <script>
        var map = L.map('map', {
            zoomControl: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var userMarker;
        var userPopup = L.popup().setContent("You are here");

        map.setView([0, 0], 10);

        function updateUserMarker(position) {
            const { coords } = position;

            if (!userMarker) {
                userMarker = L.marker([coords.latitude, coords.longitude]).addTo(map);
                userMarker.bindPopup(userPopup).openPopup();
            } else {
                userMarker.setLatLng([coords.latitude, coords.longitude]);
            }

            map.setView([coords.latitude, coords.longitude], 15);

            userPopup.setContent(`You are here<br>Fare: ₱<b><?= $fare ?></b><br>Distance: <?= $distance ?> km`);
        }

        function handleLocationError(error) {
            console.error('Error getting user location:', error);
        }

        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(updateUserMarker, handleLocationError);
        }

        const greenMarkerIcon = L.icon({
            iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png",
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        const redMarkerIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });

        
            var pickupMarker = L.marker([<?= $pickupCoords[0] ?>, <?= $pickupCoords[1] ?>], { icon: greenMarkerIcon }).addTo(map);
            var dropoffMarker = L.marker([<?= $dropoffCoords[0] ?>, <?= $dropoffCoords[1] ?>], { icon: redMarkerIcon }).addTo(map);

            pickupMarker.bindPopup('<b>Pickup</b>').openPopup();
            dropoffMarker.bindPopup('<b>Drop-off</b>').openPopup();

            map.setView([<?= $pickupCoords[0] ?>, <?= $pickupCoords[1] ?>], 15);

            var routeUrl = `https://router.project-osrm.org/route/v1/driving/${<?= $pickupCoords[1] ?>},${<?= $pickupCoords[0] ?>};${<?= $dropoffCoords[1] ?>},${<?= $dropoffCoords[0] ?>}?overview=full&geometries=geojson`;
            fetch(routeUrl)
                .then(response => response.json())
                .then(data => {
                    L.geoJSON(data.routes[0].geometry, {}).addTo(map);
                })
                .catch(error => console.error('Error fetching route data:', error));
        

        // Check if the marker is less than 10 meters from the drop-off point and redirect to receipt.php
        function checkDistance() {
            var userLatLng = userMarker.getLatLng();
            var dropoffLatLng = dropoffMarker.getLatLng();

            var distance = userLatLng.distanceTo(dropoffLatLng);

            if (distance < 10) {
                window.location.href = 'receipt.php';
            }
        }

        // Check the distance periodically
        setInterval(checkDistance, 5000);
    </script>
</body>
</html>