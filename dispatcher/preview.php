<!DOCTYPE html>
<html>


<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dispatcher | Preview</title>
    <link rel="icon" href="../img/logo3.png" type="image/png" />
    <link rel="stylesheet" href="../css/dispreview.css" />
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
    include 'disnav.php';
    ?>
    <div id="map" style="width: 100%; height: 400px;"></div>
    <h1 id="pickupInfo"></h1>
    <h1 id="dropoffInfo"></h1>
    <h1 id="terminalInfo">Terminal:</h1>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const bookingId = urlParams.get("bookingid");

        const map = L.map('map', {

            zoomControl: false,
            doubleClickZoom: false

        }).setView([0, 0], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var greenMarkerIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });

        var redMarkerIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });

        fetch('get_coordinates.php?bookingid=' + bookingId)
            .then(response => response.json())
            .then(data => {
                const pickupPoint = data.pickupPoint;
                const dropoffPoint = data.dropoffPoint;
                const terminalPoint = data.terminal;

                document.getElementById("pickupInfo").textContent += `  ${pickupPoint}`;
                document.getElementById("dropoffInfo").textContent += `  ${dropoffPoint}`;
                document.getElementById("terminalInfo").textContent += `  ${terminalPoint.latlng.lat},${terminalPoint.latlng.lng}`;

                L.marker(pickupPoint).addTo(map)
                    .bindPopup('Pickup')
                    .setIcon(greenMarkerIcon).openPopup();

                L.marker(dropoffPoint).addTo(map)
                    .bindPopup('Drop-off')
                    .setIcon(redMarkerIcon);

                L.marker(terminalPoint.latlng).addTo(map)
                    .bindPopup('Terminal')

                map.setView(terminalPoint.latlng, 15);

                // getShortestRoute(pickupPoint.latlng, dropoffPoint.latlng);

            })
            .catch(error => {
                console.error('Error fetching coordinates:', error);
            });



    </script>
</body>

</html>