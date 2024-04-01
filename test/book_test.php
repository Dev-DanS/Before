<?php
include('../php/session_commuter.php');

include '../db/tdbconn.php';

$todaQuery = "SELECT Toda, Coordinates FROM todaterminal";
$todaResult = mysqli_query($conn, $todaQuery);

$todaLocations = [];

while ($tl = mysqli_fetch_assoc($todaResult)) {
    $todaLocations[] = ['Toda' => $tl['Toda'], 'Coordinates' => json_decode($tl['Coordinates'], true)];
}

$todalocationData = json_encode($todaLocations);

$borderQuery = "SELECT border FROM province LIMIT 1";
$borderResult = mysqli_query($conn, $borderQuery);

if ($borderResult) {
    $border = mysqli_fetch_assoc($borderResult)['border'];
} else {
    echo "Error fetching border data: " . mysqli_error($conn);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Commuter | Book</title>
    <link rel="icon" href="../img/logo3.png" type="image/png" />
    <link rel="stylesheet" href="../css/tcbooks.css" />
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
    <div class="search">
        <input id="search-input" type="text" placeholder="Where are you heading to?">
        <button id="search-button"><i class="fa-solid fa-magnifying-glass fa-lg" style="color: #ffffff;"></i>
        </button>
    </div>
    <div id="map" style="width: 100%; height: 60vh;"></div>

    <div class="main">
        <div class="side1">
            <p class="pickup">
                <i class="fa-solid fa-circle-dot fa-lg" style="color: #247dc9;"></i> Current Location
            </p>
            <div class="change" id="change-location">
                <i class="fa-solid fa-map-location-dot fa-xl custom-color" style="color: #247dc9;"></i>
            </div>
        </div>

        <div class="line">
            <hr>
        </div>

        <div class="side2">
            <p class="destination">
                <i class="fa-solid fa-location-dot fa-lg" style="color: #c81e31;"></i> Tap map twice to add destination
            </p>
        </div>
    </div>

    <div class="dropdown-center">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-users" style="color: #ffffff;"></i> Passenger(s): <span id="passenger-display">1</span>
        </button>
        <ul class="dropdown-menu" id="passenger-dropdown">
            <li><a class="dropdown-item" href="#" data-value="1">1</a></li>
            <li><a class="dropdown-item" href="#" data-value="2">2</a></li>
            <li><a class="dropdown-item" href="#" data-value="3">3</a></li>
            <li><a class="dropdown-item" href="#" data-value="4">4</a></li>
        </ul>
    </div>

    <div class="confirm">
        <button type="submit" class="confirm-btn" id="next">
            <i class="fa-solid fa-check fa-lg" style="color: #ffffff;"></i> Confirm Booking
        </button>
    </div>

    <script src="../commuter/search.js"></script>

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
        let userLocation;
        let pickupAddress;
        let dropoffAddress;

        function centerMapToCurrentLocation() {
            navigator.geolocation.getCurrentPosition(function(position) {
                const {
                    latitude,
                    longitude
                } = position.coords;
                userLocation = `${latitude},${longitude}`;
                map.setView([latitude, longitude], 15);
                const blueIcon = L.divIcon({
                    className: 'custom-icon',
                    html: '<i class="fa-solid fa-circle-dot"></i>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10],
                    popupAnchor: [0, -10]
                });

                const marker = L.marker([latitude, longitude], {
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

                marker.bindPopup("You are here").openPopup();
                // checkInsidePolygon(latitude, longitude);
                console.log(userLocation);

                $.ajax({
                    url: 'https://nominatim.openstreetmap.org/reverse',
                    method: 'GET',
                    dataType: 'json',
                    data: {
                        format: 'json',
                        lat: latitude,
                        lon: longitude,
                        zoom: 18,
                    },
                    success: function(data) {
                        var address = data.display_name;
                        var addressParts = address.split(',');
                        var firstThreeParts = addressParts.slice(0, 2);
                        var shortenedAddress = firstThreeParts.join(',');

                        pickupAddress = shortenedAddress;
                        console.log(pickupAddress);
                    },
                    error: function(error) {
                        console.error('Error getting address: ' + error);
                    }
                });
            });
        }

        centerMapToCurrentLocation();

        function checkInsidePolygon(lat, lng) {
            const polygonData = <?php echo json_encode(json_decode($border, true)); ?>;
            const isInside = L.polygon(polygonData.latlngs).getBounds().contains(L.latLng(lat, lng));

            if (!isInside) {
                const alertContent = "Our services are only available within Baliuag.";
                alert(alertContent);

                window.location.href = '../commuter/commuter.php';
            }
        }

        function isPointInsidePolygon(point, polygon) {
            const {
                lat,
                lng
            } = point;
            const polygonVertices = polygon.getLatLngs()[0];

            let intersectCount = 0;

            for (let i = 0; i < polygonVertices.length - 1; i++) {
                const [vertex1, vertex2] = [polygonVertices[i], polygonVertices[i + 1]];

                if ((vertex1.lat <= lat && lat < vertex2.lat || vertex2.lat <= lat && lat < vertex1.lat) &&
                    (lng < (vertex1.lng - vertex2.lng) * (lat - vertex2.lat) / (vertex1.lat - vertex2.lat) + vertex2.lng)) {
                    intersectCount++;
                }
            }

            return intersectCount % 2 === 1;
        }

        function displayBaliuagBorder() {
            const polygonData = <?php echo json_encode(json_decode($border, true)); ?>;

            L.polyline(polygonData.latlngs, {
                color: 'red',
                weight: 1,
                dashArray: '10, 5',
                opacity: 1,
                lineCap: 'round',
            }).addTo(map);
        }

        displayBaliuagBorder();

        let dropoffMarker;
        map.on('dblclick', function(e) {
            const {
                lat,
                lng
            } = e.latlng;
            dropoffPoint = L.latLng(lat, lng);

            $.ajax({
                url: 'https://nominatim.openstreetmap.org/reverse',
                method: 'GET',
                dataType: 'json',
                data: {
                    format: 'json',
                    lat: lat,
                    lon: lng,
                    zoom: 18,
                },
                success: function(data) {
                    let address = data.display_name;
                    let addressParts = address.split(',');
                    let firstThreeParts = addressParts.slice(0, 2);
                    let shortenedAddress = firstThreeParts.join(',');

                    var iconHTML = '<i class="fa-solid fa-location-dot fa-lg" style="color: #c81e31;"></i> ';
                    $('.destination').html(iconHTML + shortenedAddress);


                    dropoffAddress = shortenedAddress;
                    console.log(dropoffAddress);
                },
                error: function(error) {
                    console.error('Error getting address: ' + error);
                }
            });

            const isInsideBorder = isPointInsidePolygon({
                lat,
                lng
            }, L.polygon(<?php echo json_encode(json_decode($border, true)); ?>.latlngs));

            if (isInsideBorder) {
                if (dropoffMarker) map.removeLayer(dropoffMarker);
                dropoffMarker = L.marker([lat, lng], {
                    icon: redMarkerIcon
                }).addTo(map);
                document.getElementById("next").style.display = "block";
            } else {
                alert("Uh oh! Looks like your chosen drop-off location is outside of our service area in Baliuag.");
            }
        });

    </script>
</body>

</html>