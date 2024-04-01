<?php
include('../php/session_commuter.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commuter | Scan QR</title>
    <link rel="icon" href="../img/logo3.png" type="image/png" />
    <link rel="stylesheet" href="../css/cscan.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet" />
    <script src="https://kit.fontawesome.com/965a209c77.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@zxing/library@0.19.1"></script>
</head>

<body>
    <?php
    include 'cnav.php';
    ?>
    <div class="video">
        <video id="qr-video" width="auto" height="500px"></video>
    <div id="result"></div>
    </div>
    

    <div class="confirm">
        <button class="confirm-btn" id="confirm-btns">
            <i class="fa-solid fa-angle-left fa-lg" style="color: #ffffff;"></i> Back
        </button>
    </div>

    <script>
        // Function to handle the QR code scanning
        document.getElementById("confirm-btns").addEventListener("click", function () {
            window.location.href = "found.php";
        });

        const videoElement = document.getElementById('qr-video');
        const resultElement = document.getElementById('result');

        const codeReader = new ZXing.BrowserQRCodeReader();

        codeReader
            .decodeFromVideoDevice(undefined, 'qr-video', (result, err) => {
                if (result) {
                    resultElement.innerHTML = `QR Code Result: ${result.text}`;

                    // Add the code to redirect and compare the QR result here
                    // You can add it directly below this comment

                    // Example: Redirect to the PHP script with the QR result as a parameter
                    window.location.href = 'scanback.php?qr_result=' + result.text;
                }
                if (err && !(err instanceof ZXing.NotFoundException)) {
                    console.error('QR Code scan error:', err);
                    resultElement.innerHTML = 'Error scanning QR code.';
                }
            })
            .catch(error => {
                console.error('Error accessing the camera:', error);
                resultElement.innerHTML = 'Error accessing the camera.';
            });
    </script>
</body>

</html>