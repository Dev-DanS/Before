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
                <i class="fa-solid fa-map-location-dot fa-xl custom-color" id="custom-color" style="color: #247dc9;"></i>
            </div>
        </div>

        <div class="line">
            <hr>
        </div>

        <div class="side2">
            <p class="destination">
                <i class="fa-solid fa-location-dot fa-lg" style="color: #c81e31;"></i> Tap map twice to add Destination
            </p>
        </div>
    </div>

    <div class="container">
        <div class="row no-stack">
            <div class="col-md-6">
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
            </div>
            <div class="col-md-6">
                <div class="confirm">
                    <button type="submit" class="confirm-btn" id="next">
                        <i class="fa-solid fa-check fa-lg" style="color: #ffffff;"></i> Confirm Booking
                    </button>
                </div>
            </div>
        </div>
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

        let pickupPoint;
        let dropoffPoint;
        let userLocation;

        let pickupAddress;
        let dropoffAddress;
        let myLocationAddress;

        let pickupPointRoute;
        let dropoffPointRoute;
        let userLocationPointRoute;

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

        function centerMapToCurrentLocation() {
            navigator.geolocation.getCurrentPosition(function(position) {
                const {
                    latitude,
                    longitude
                } = position.coords;
                userLocation = `${latitude},${longitude}`;
                userLocationPointRoute = L.latLng(latitude, longitude);
                map.setView([latitude, longitude], 15);
                findNearestTODA(latitude, longitude);
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
                console.log('My Location Coords: ' + userLocation);

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

                        myLocationAddress = shortenedAddress;
                        console.log('My Location address: ' + myLocationAddress);
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

                window.location.href = 'commuter.php';
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
        let routeLayer;

        map.on('dblclick', function(e) {
            if (!isChangeLocationClicked) {
                const {
                    lat,
                    lng
                } = e.latlng;
                dropoffPointRoute = L.latLng(lat, lng);
                dropoffPoint = `${lat},${lng}`;

                const isInsideBorder = isPointInsidePolygon({
                    lat,
                    lng
                }, L.polygon(<?php echo json_encode(json_decode($border, true)); ?>.latlngs));

                if (isInsideBorder) {
                    // Check if pickupPointRoute is null or empty
                    const pickupPointRouteToUse = (pickupPointRoute === null || pickupPointRoute === undefined || pickupPointRoute === '') ? userLocationPointRoute : pickupPointRoute;

                    const url = `https://router.project-osrm.org/route/v1/driving/${pickupPointRouteToUse.lng},${pickupPointRouteToUse.lat};${dropoffPointRoute.lng},${dropoffPointRoute.lat}?overview=full&geometries=geojson`;

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
                                    weight: 5,
                                    color: '#03b14e'
                                }
                            }).addTo(map);

                            const boundsWithPadding = routeLayer.getBounds().pad(0.10); // 10% padding
                            map.fitBounds(boundsWithPadding);

                            calculateFare(distance)
                        })
                        .catch(error => {
                            console.error('Error fetching route from OSRM:', error);
                        });

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
                            console.log('Dropoff address: ' + dropoffAddress);
                            console.log('Dropoff Coords: ' + dropoffPoint);
                        },
                        error: function(error) {
                            console.error('Error getting address: ' + error);
                        }
                    });

                    if (dropoffMarker) map.removeLayer(dropoffMarker);
                    dropoffMarker = L.marker([lat, lng], {
                        icon: redMarkerIcon
                    }).addTo(map);
                    dropoffMarker.bindPopup("<div style='text-align: center;'>Dropoff</div>").openPopup();
                    document.getElementById("next").style.display = "block";
                } else {
                    alert("Uh oh! Looks like your chosen drop-off location is outside of our service area in Baliuag.");
                }
            }
        });

        let isChangeLocationClicked = false;
        let pickupMarker;

        document.getElementById("change-location").addEventListener("click", function() {
            document.getElementById("custom-color").style.color = "#03b14e";
            let iconHTML = '<i class="fa-solid fa-location-dot fa-lg" style="color: #247dc9;"></i> ';
            $('.pickup').html(iconHTML + 'Tap map twice to add Pickup');
            isChangeLocationClicked = true;
        });

        map.on('dblclick', function(e) {
            if (isChangeLocationClicked) {
                const {
                    lat,
                    lng
                } = e.latlng;
                pickupPoint = `${lat},${lng}`;
                pickupPointRoute = L.latLng(lat, lng);
                isChangeLocationClicked = false; // Reset the flag

                const isInsideBorder = isPointInsidePolygon({
                    lat,
                    lng
                }, L.polygon(<?php echo json_encode(json_decode($border, true)); ?>.latlngs));

                if (isInsideBorder) {
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

                            var iconHTML = '<i class="fa-solid fa-location-dot fa-lg" style="color: #247dc9;"></i> ';
                            $('.pickup').html(iconHTML + shortenedAddress);
                            document.getElementById("custom-color").style.color = "#247dc9";

                            pickupAddress = shortenedAddress;
                            console.log('Pickup address: ' + pickupAddress);
                            console.log('Pickup Coords: ' + pickupPoint);
                        },
                        error: function(error) {
                            console.error('Error getting address: ' + error);
                        }
                    });

                    if (pickupMarker) map.removeLayer(pickupMarker);
                    pickupMarker = L.marker([lat, lng], {
                        icon: blueIcon
                    }).addTo(map);
                    pickupMarker.bindPopup("<div style='text-align: center;'>Pickup</div>").openPopup();

                    const url = `https://router.project-osrm.org/route/v1/driving/${pickupPointRoute.lng},${pickupPointRoute.lat};${dropoffPointRoute.lng},${dropoffPointRoute.lat}?overview=full&geometries=geojson`;

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
                                    weight: 5,
                                    color: '#03b14e'
                                }
                            }).addTo(map);

                            const boundsWithPadding = routeLayer.getBounds().pad(0.10); // 10% padding
                            map.fitBounds(boundsWithPadding);

                            calculateFare(distance)
                        })
                        .catch(error => {
                            console.error('Error fetching route from OSRM:', error);
                        });
                } else {
                    alert("Uh oh! Looks like your chosen pick-up location is outside of our service area in Baliuag.");
                }
            }
        });

        let nearestTODA;
        let nearestLatLng;

        function findNearestTODA(latitude, longitude) {
            const todalocations = <?php echo $todalocationData; ?>;

            let minDistance = Infinity;


            todalocations.forEach((location) => {
                const {
                    lat,
                    lng
                } = location.Coordinates.latlng;
                const distance = L.latLng(latitude, longitude).distanceTo([lat, lng]);

                if (distance < minDistance) {
                    nearestTODA = location.Toda;
                    minDistance = distance;
                    nearestLatLng = {
                        lat,
                        lng
                    };
                }
            });

            console.log(`Nearest TODA: ${nearestTODA}`);
            // console.log(`Distance to Nearest TODA: ${minDistance} meters`);
            console.log(`Nearest TODA Coordinates:`, nearestLatLng);
            calculateDistanceAndDisplayPopup()
        }

        let distanceToda;
        let distanceToNearestTODA;

        function calculateDistanceAndDisplayPopup() {
            const url = `https://router.project-osrm.org/route/v1/driving/${userLocationPointRoute.lng},${userLocationPointRoute.lat};${nearestLatLng.lng},${nearestLatLng.lat}?overview=full&geometries=geojson`;

            axios.get(url)
                .then(response => {
                    distanceToNearestTODA = response.data.routes[0].distance / 1000; // Convert meters to kilometers
                    distanceToda = `Distance to Nearest TODA: ${distanceToNearestTODA.toFixed(2)} km`;
                    console.log(`Nearest TODA Distance: ${distanceToNearestTODA.toFixed(2)} km`);
                    // console.log(`HUH: ${distanceToNearestTODA} km`); 

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

        let distance;
        let fare;
        let durationMinutes;
        let convenienceFee;
        let grandTotal;

        function calculateFare(distance) {
            const currentTime = new Date();
            const isNightTime = currentTime.getHours() >= 23 || currentTime.getHours() < 4;

            // if (isNightTime) {
            //     fare = Math.round((distance - 2) * (perKM + nightDiff));
            // } else {
            //     fare = Math.round((distance - 2) * perKM);
            // }

            if (distance <= 2) {
                fare = baseFare + ((passengerCount > 1 ? (passengerCount - 1) * farePerPassenger : 0));
            } else {
                fare = Math.round(baseFare + (distance - 2) * perKM);
                fare += (passengerCount > 1 ? (passengerCount - 1) * farePerPassenger : 0);
            }

            convenienceFee = distanceToNearestTODA * perKM;
            grandTotal = Math.round(fare + convenienceFee);
            // console.log(fare);
            console.log('Grandtotal', grandTotal);
            console.log('convenienceFee', convenienceFee);
            // console.log('isNightTime', isNightTime);

            dropoffMarker.bindPopup(`<b><div style="text-align: center; ">Drop-off</div></b>Distance: ${distance} km<br>ETA: ${durationMinutes} minutes<br>Fare: <b>₱${grandTotal}</b>`).openPopup();

        }

        function checkStatus() {
            $.ajax({
                url: "book_status.php",
                method: "POST",
                success: function (response) {
                    if (response === 'accepted') {
                        window.location.href = "tfound.php";
                    } else if (response === 'ongoing') {
                        window.location.href = "tongoing.php";
                    } else if (response === 'pending') {
                        window.location.href = "newsearching.php";
                    }else {
                        setTimeout(checkStatus, 2000);
                    }
                }
            });
        }

        checkStatus();

        $(document).ready(function() {
            $("#next").click(function() {
                let pickupPointsToSend = pickupPoint;
                let pickupAddressToSend = pickupAddress;

                if (!pickupPoint) {
                    pickupPointsToSend = userLocation;
                }

                if (!pickupAddress) {
                    pickupAddressToSend = myLocationAddress;
                }

                const dataToSend = {
                    nearestTODA: nearestTODA,
                    pickupPoints: pickupPointsToSend,
                    dropoffPoints: dropoffPoint,
                    grandTotal: grandTotal,
                    passengerCount: passengerCount,
                    durationMinutes: durationMinutes,
                    distance: distance,
                    pickupAddress: pickupAddressToSend,
                    dropoffAddress: dropoffAddress
                };

                // Ask user for confirmation
                if (confirm("Are you sure you want to proceed?")) {
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
                }
            });
        });
    </script>
</body>

</html>