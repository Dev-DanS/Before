<?php
include('../php/session_commuter.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commuter | Receipt</title>
    <link rel="icon" href="../img/logo3.png" type="image/png" />
    <link rel="stylesheet" href="../css/creceipt.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap"
        rel="stylesheet" />
    <script src="https://kit.fontawesome.com/965a209c77.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php
    include 'cnav.php';
    ?>
    <?php
    include 'receiptback.php';
    ?>
    <div class="container">
        <h1>Your Receipt</h1>
        <table>
            <tr>
                <td>Body Number</td>
                <td>
                    <?php echo $plateNumber; ?>
                </td>
            </tr>
            <tr>
                <td>Toda</td>
                <td>
                    <?php echo $toda; ?>
                </td>
            </tr>
            <tr>
                <td>Distance</td>
                <td>
                    <?php echo $distance; ?>
                </td>
            </tr>
        </table>

        <p>Total Amount: ₱
            <?php echo $fare; ?>
        </p>
    </div>
</body>

</html>