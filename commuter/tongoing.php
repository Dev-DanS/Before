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
    <link rel="stylesheet" href="../css/tcongoing.css">
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
    <script>
        function checkStatus() {
            $.ajax({
                url: "book_status.php",
                method: "POST",
                success: function(response) {
                    if (response === 'pending') {
                        window.location.href = "newsearching.php";
                    } else if (response === 'accepted') {
                        window.location.href = "tfound.php";
                    } else {
                        setTimeout(checkStatus, 2000);
                    }
                }
            });
        }

        checkStatus();
    </script>
    <?php
    include 'tongoingback.php';
    ?>

    <div id="map" style="width: 100%; height: 65vh;"></div>

    <div class="rideData">
        <div class="rideText">
            <p class="otw"><i class="fa-solid fa-location-dot fa-xl" style="color: #ff0000;"></i> <?php echo $dropoffaddress; ?> •<strong> <span id="time">4 mins</span></strong>
            </p>
            <p class="time">₱ <?php echo $fare; ?></p>
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

    <div class="endTrip">
        <button type="submit" class="endTrip-btn" id="endTrip">
            End Trip Early
        </button>
    </div>

    <div class="start">
        <button type="submit" class="start-btn" id="start">
            Destination Reached
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

        // document.getElementById('endTrip').addEventListener('click', function() {
        //     var confirmEnd = confirm('Ending your trip early will charge you for the distance traveled so far. Cancellation Fee ₱ 100.00');
        //     if (confirmEnd) {
        //         this.disabled = true;
        //         console.log('Ending the trip early...');
        //     }
        // });

        const createMarkerIcon = (color) => L.icon({
            iconUrl: `https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-${color}.png`,
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        const blueIcon = createMarkerIcon('blue');
        const greenMarkerIcon = createMarkerIcon('green');
        const redMarkerIcon = createMarkerIcon('red');


        let pickupPointStr = '<?php echo $dropoffPoint; ?>';
        let [lat, lng] = pickupPointStr.split(',').map(Number);

        let pickupPoint = {
            lat: lat,
            lng: lng
        };

        let destination = `${lat},${lng}`;

        // console.log(pickupPoint);
        console.log('Destination Coords', destination);

        let driverLoc;

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

            const distanceToPickup = map.distance([latitude, longitude], [pickupPoint.lat, pickupPoint.lng]);

            if (distanceToPickup <= 20) {
                document.getElementById('endTrip').style.display = 'none'; // Hide cancel button
                document.getElementById('start').style.display = 'block'; // Show destination reached button
            } else {
                document.getElementById('endTrip').style.display = 'block'; // Show cancel button
                document.getElementById('start').style.display = 'none'; // Hide destination reached button
            }

            // console.log('Current ', driverLoc);

            let driverlive = `${latitude},${longitude}`;

            console.log('Current Location: ', driverlive);

            marker.setLatLng([latitude, longitude]);

            // const distanceToPickup = map.distance([latitude, longitude], [pickupPoint.lat, pickupPoint.lng]);


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

        document.getElementById('start').addEventListener('click', function() {
            // Redirect to hello.html
            window.location.href = 'tongoingbackreached.php';
        });

        let userLocation;
        startTracking();

        // function centerMapToCurrentLocation() {
        //     navigator.geolocation.getCurrentPosition(function(position) {
        //         const {
        //             latitude,
        //             longitude
        //         } = position.coords;
        //         userLocation = L.latLng(latitude, longitude);
        //         console.log('Starting Point Coords: ' + userLocation);
        //     });
        // }

        // centerMapToCurrentLocation()
        

        // let baseFare;
        // let perKM;
        // let nightDiff;
        // let farePerPassenger;

        // $.ajax({
        //     url: 'farematrix.php',
        //     method: 'GET',
        //     dataType: 'json',
        //     success: function(response) {
        //         if (response.error) {
        //             console.error(response.error);
        //         } else {
        //             baseFare = parseInt(response.baseFare);
        //             perKM = parseInt(response.perKM);
        //             nightDiff = parseInt(response.nightDiff);
        //             farePerPassenger = parseInt(response.farePerPassenger);

        //             console.log('Base Fare:', baseFare);
        //             console.log('Per KM:', perKM);
        //             console.log('Night Differential:', nightDiff);
        //             console.log('Fare Per Passenger:', farePerPassenger);
        //         }
        //     },
        //     error: function(xhr, status, error) {
        //         console.error("Farematrix request failed:", error);
        //     }
        // });

        let canceldistance;
        $(document).ready(function() {
            $("#endTrip").click(function() {
                if (confirm("Are you sure you want to cancel this booking?")) {
                    // const url = `https://router.project-osrm.org/route/v1/driving/${userLocation.lng},${userLocation.lat};${driverLoc.lng},${driverLoc.lat}?overview=full&geometries=geojson`;

                    // axios.get(url)
                    //     .then(response => {
                    //         canceldistance = (response.data.routes[0].distance / 1000).toFixed(2);
                    //         console.log("Cancel Distance", canceldistance);
                    //     })
                    //     .catch(error => {
                    //         console.error('Error fetching route from OSRM:', error);
                    //     });
                    $.ajax({
                        url: "tongoing_cancel.php",
                        method: "POST",
                        success: function(response) {
                            alert(response);
                            window.location.href = "treceipt_cancel.php";
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>