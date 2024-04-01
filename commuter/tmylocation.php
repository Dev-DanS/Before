<?php
include('../php/session_commuter.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commuter | My Location</title>
    <link rel="icon" href="../img/logo3.png" type="image/png" />
    <link rel="stylesheet" href="../css/tcmylocation.css" />
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

    <div id="map" style="width: 100%; height: 65vh;"></div>

    <div class="rideInfo">
        <div class="address">
            <p class="loc"><i class="fa-solid fa-circle-dot fa-lg" style="color: #247dc9;"></i> Locating your current
                address...</p>
        </div>
    </div>

    <div class="endTrip">
        <button type="submit" class="endTrip-btn" id="endTrip" onclick="window.location.href='commuter.php';">
            Go back?
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
        let dropoffPoint;

        let userMarker;

        if ('geolocation' in navigator) {
            const watchId = navigator.geolocation.watchPosition(
                ({
                    coords: {
                        latitude: userLat,
                        longitude: userLng
                    }
                }) => {
                    pickupPoint = [userLat, userLng];
                    if (userMarker) map.removeLayer(userMarker);

                    const blueIcon = L.divIcon({
                    className: 'custom-icon',
                    html: '<i class="fa-solid fa-circle-dot"></i>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10],
                    popupAnchor: [0, -10]
                });

                    userMarker = L.marker([userLat, userLng], {
                        icon: blueIcon
                    }).addTo(map);

                    const customIconStyle = `.custom-icon {
                color: #247dc9; 
                font-size: 20px; 
                } 
                `;

                const styleElement = document.createElement('style');
                styleElement.innerHTML = customIconStyle;
                document.head.appendChild(styleElement);
                    userMarker.bindPopup('You are here').openPopup();
                    map.setView([userLat, userLng], 16);
                    userMarker.bindPopup('You are here').openPopup();
                    map.setView([userLat, userLng], 16);


                    console.log(`Pickup Point: ${pickupPoint}`);

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
                            // Split the address into parts
                            var addressParts = address.split(',');
                            // Get the first three parts of the address
                            var firstThreeParts = addressParts.slice(0, 3);
                            // Join the first three parts back into a string
                            var shortenedAddress = firstThreeParts.join(',');

                            // Update the p element with the shortened address
                            var iconHTML = '<i class="fa-solid fa-circle-dot fa-lg" style="color: #247dc9;"></i> ';
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
    </script>
</body>

</html>