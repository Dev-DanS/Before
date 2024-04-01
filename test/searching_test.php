<?php
include('../php/session_commuter.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
<div id="countdown">Loading...</div>

    <script>
        $(document).ready(function() {
    function updateCountdown() {
        $.ajax({
            url: 'searching_testback.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.expired) {
                    $('#countdown').text('Expired');
                } else {
                    $('#countdown').text(data.remainingTime);
                }
            },
            error: function() {
                console.log('Error fetching remaining time.');
            }
        });
    }

    // Update countdown every second
    setInterval(updateCountdown, 1000);
});

    </script>
</body>
</html>