<?php
include('../php/session_driver.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver | Manage Account</title>
    <link rel="icon" href="../img/logo3.png" type="image/png" />
    <link rel="stylesheet" href="../css/tcmanage.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/965a209c77.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php
    include 'dnav.php';
    include 'dmanageback.php';
    ?>

    <div class="menu">
        <div class="info">
            <h5 class="label">Manage Account</h5>
            <div class="user">
                <p class="name"><i class="fa-solid fa-user" style="color: #03b14e;"></i> <?php echo $firstname; ?> <?php echo $lastname; ?></p>
                <p class="email"><i class="fa-solid fa-envelope" style="color: #03b14e;"></i> <?php echo $email; ?></p>
                <p class="mobile"><i class="fa-solid fa-sim-card" style="color: #03b14e;"> </i> <?php echo $mobilenumber; ?></p>
            </div>


            <h5 class="label2">Credentials</h5>

            <form id="passwordForm" method="POST" action="tmanageback2.php">
                <div class="input">
                    <p class="warning"></p>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="currentPassword" id="password" placeholder="Password">
                        <label for="password">Current Password</label>
                    </div>
                    <br>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="newPassword" id="confirmPassword" placeholder="Confirm Password">
                        <label for="confirmPassword">New Password</label>
                    </div>

                    <div class="confirm">
                        <button type="submit" class="confirm-btn">
                            <i class="fa-solid fa-check fa-lg" style="color: #ffffff;"></i> Confirm Change
                        </button>
                    </div>
                </div>
            </form>





        </div>

    </div>
    <script>
        // Function to get URL parameter by name
        function getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }

        // Check for error parameter in URL and show alert if present
        window.onload = function() {
            var error = getParameterByName('error');
            if (error) {
                alert(error);
            }
        };
    </script>
</body>

</html>