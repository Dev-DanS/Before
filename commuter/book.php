<?php
include('../php/session_commuter.php');
?>
<?php
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
    <link rel="stylesheet" href="../css/cbook.css" />
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
        <button type="submit" class="confirm-btn" id="generateData">
            <i class="fa-solid fa-check fa-lg" style="color: #ffffff;"></i> Confirm Booking
        </button>
    </div>




    <script src="search.js"></script>
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


        var pickupPoint;
        var pickupPoints;
        var dropoffPoint;
        var dropoffPoints;
        var noPassenger;

        let passengerCount = 1;

        document.getElementById("passenger-dropdown").addEventListener("click", (e) => {
            if (e.target && e.target.nodeName === "A") {
                passengerCount = parseInt(e.target.getAttribute("data-value")); // Convert to integer
                document.getElementById("passenger-display").innerText = passengerCount;

                console.log(`Passenger Count: ${passengerCount}`);

                if (typeof dropoffMarker !== 'undefined' && dropoffMarker !== null) {
                    calculateFare(distance);
                } else {
                    console.log("Dropoff doesn't exist. Cannot calculate fare.");
                }
            }
        });

        let userMarker;
        let pickupAddress;
        let dropoffAddress;

        if ('geolocation' in navigator) {
            const watchId = navigator.geolocation.watchPosition(
                ({
                    coords: {
                        latitude: userLat,
                        longitude: userLng
                    }
                }) => {
                    pickupPoint = [userLat, userLng];
                    pickupPoints = `${userLat},${userLng}`;

                    // Remove the existing user marker if it exists
                    if (userMarker) map.removeLayer(userMarker);

                    userMarker = L.marker([userLat, userLng], {
                        icon: greenMarkerIcon
                    }).addTo(map);
                    userMarker.bindPopup('You are here').openPopup();

                    // checkInsidePolygon(userLat, userLng);
                    findNearestTODA(userLat, userLng);

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
                            var firstThreeParts = addressParts.slice(0, 2);
                            var shortenedAddress = firstThreeParts.join(',');

                            // var iconHTML = '<i class="fa-solid fa-location-dot fa-lg" style="color: #ffff;"></i> ';
                            // $('.address p').html(iconHTML + shortenedAddress);


                            pickupAddress = shortenedAddress;
                            console.log(pickupAddress);
                        },
                        error: function(error) {
                            console.error('Error getting address: ' + error);
                        }
                    });

                    console.log(`Pickup Point: ${pickupPoint}`);
                },
                (error) => {
                    console.error(`Error getting User's location: ${error.message}`);
                }
            );
        }


        function checkInsidePolygon(lat, lng) {
            const polygonData = <?php echo json_encode(json_decode($border, true)); ?>;
            const isInside = L.polygon(polygonData.latlngs).getBounds().contains(L.latLng(lat, lng));

            if (!isInside) {
                const alertContent = "It seems you're currently outside of Baliuag, where Trisakay's services are currently unavailable. To book a ride, please head back to Baliuag or check back later for updates on our expanded coverage area.\n\nIn the meantime, you can check out other modes of transportation available in your location.\n\nThank you for your understanding!";
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

        let nearestTODA;
        let nearestLatLng;

        function findNearestTODA(userLat, userLng) {
            const todalocations = <?php echo $todalocationData; ?>;

            let minDistance = Infinity;


            todalocations.forEach((location) => {
                const {
                    lat,
                    lng
                } = location.Coordinates.latlng;
                const distance = L.latLng(userLat, userLng).distanceTo([lat, lng]);

                if (distance < minDistance) {
                    nearestTODA = location.Toda;
                    minDistance = distance;
                    nearestLatLng = {
                        lat,
                        lng
                    };
                }
            });

            // console.log(`Nearest TODA: ${nearestTODA}`); 
            // console.log(`Distance to Nearest TODA: ${minDistance} meters`);
            // console.log(`Nearest TODA Coordinates:`, nearestLatLng);
            calculateDistanceAndDisplayPopup()
        }

        let distanceToda;
        let distanceToNearestTODA;

        function calculateDistanceAndDisplayPopup() {
            const url = `https://router.project-osrm.org/route/v1/driving/${pickupPoint[1]},${pickupPoint[0]};${nearestLatLng.lng},${nearestLatLng.lat}?overview=full&geometries=geojson`;

            axios.get(url)
                .then(response => {
                    distanceToNearestTODA = response.data.routes[0].distance / 1000; // Convert meters to kilometers
                    distanceToda = `Distance to Nearest TODA: ${distanceToNearestTODA.toFixed(2)} km`;

                })
                .catch(error => {
                    console.error('Error fetching route from OSRM:', error);
                });
        }

        let baseFare;
        let perKM;
        let nightDiff;
        let farePerPassenger;

        $.ajax({
            url: 'farematrix.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error(response.error);
                } else {
                    baseFare = parseInt(response.baseFare);
                    perKM = parseInt(response.perKM);
                    nightDiff = parseInt(response.nightDiff);
                    farePerPassenger = parseInt(response.farePerPassenger);

                    console.log('Base Fare:', baseFare);
                    console.log('Per KM:', perKM);
                    console.log('Night Differential:', nightDiff);
                    console.log('Fare Per Passenger:', farePerPassenger);
                }
            },
            error: function(xhr, status, error) {
                console.error("Farematrix request failed:", error);
            }
        });

        let dropoffMarker; //so only one can exist
        let distance;
        let grandTotal;

        let routeLayer;
        let fare;
        let durationMinutes;

        map.on('dblclick', function(e) {
            const {
                lat,
                lng
            } = e.latlng;
            dropoffPoint = L.latLng(lat, lng);
            dropoffPoints = `${lat},${lng}`;

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

                            // var iconHTML = '<i class="fa-solid fa-location-dot fa-lg" style="color: #ffff;"></i> ';
                            // $('.address p').html(iconHTML + shortenedAddress);


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

                const url = `https://router.project-osrm.org/route/v1/driving/${pickupPoint[1]},${pickupPoint[0]};${dropoffPoint.lng},${dropoffPoint.lat}?overview=full&geometries=geojson`;

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
                                weight: 4,
                                color: '#03b14e'
                            }
                        }).addTo(map);

                        const boundsWithPadding = routeLayer.getBounds().pad(0.1); // 10% padding
                        map.fitBounds(boundsWithPadding);

                        dropoffMarker = L.marker([lat, lng], {
                            icon: redMarkerIcon
                        }).addTo(map);
                        dropoffMarker.bindPopup(`Drop-off<br>Distance: ${distance} km<br>ETA: ${durationMinutes} minutes`).openPopup();

                        console.log('Distance:', distance);
                        document.getElementById("generateData").style.display = "block";
                        calculateFare(distance)
                        console.log('Dropoff Point', dropoffPoints);

                        // console.log(`Drop-off: ${dropoffPoint}, Distance: ${distance} km, ETA: ${durationMinutes} minutes`);
                    })
                    .catch(error => {
                        console.error('Error fetching route from OSRM:', error);
                    });
            } else {
                alert("Uh oh! Looks like your chosen drop-off location is outside of our service area in Baliuag.");
            }
        });

        function calculateFare(distance) {
            const currentTime = new Date();
            const isNightTime = currentTime.getHours() >= 23 || currentTime.getHours() < 4;

            if (isNightTime) {
                fare = Math.round((distance - 2) * (perKM + nightDiff));
            } else {
                fare = Math.round((distance - 2) * perKM);
            }

            if (distance <= 2) {
                fare = baseFare + ((passengerCount > 1 ? (passengerCount - 1) * farePerPassenger : 0));
            } else {
                fare = Math.round(baseFare + (distance - 2) * perKM);
                fare += (passengerCount > 1 ? (passengerCount - 1) * farePerPassenger : 0);
            }
            grandTotal = fare
            // console.log(fare);
            console.log('Grandtotal', grandTotal);

            dropoffMarker.bindPopup(`<b><div style="text-align: center; ">Drop-off</div></b>Distance: ${distance} km<br>ETA: ${durationMinutes} minutes<br>Fare: <b>₱${fare}</b>`).openPopup();

        }

        $(document).ready(function() {
            $("#generateData").click(function() {
                const dataToSend = {
                    nearestTODA: nearestTODA,
                    pickupPoints: pickupPoints,
                    dropoffPoints: dropoffPoints,
                    grandTotal: grandTotal,
                    passengerCount: passengerCount,
                    durationMinutes: durationMinutes,
                    distance: distance,
                    pickupAddress: pickupAddress,
                    dropoffAddress: dropoffAddress
                };

                $.ajax({
                    type: "POST",
                    url: "booking_back.php",
                    data: dataToSend,
                    success: function(response) {
                        console.log("Data sent successfully to booking_back.php");
                        window.location.href = 'searching.php';
                    },
                    error: function(xhr, status, error) {
                        console.error("Error sending data:", error);
                    }
                });
            });
        });
    </script>
</body>

</html>