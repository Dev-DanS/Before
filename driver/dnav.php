<nav class="navbar navbar-expand-lg" style="background-color: #e3f2fd">
    <div class="container-fluid">
        <a class="navbar-brand" href="driver.php">
            <img src="../img/Logo2.png" alt="Bootstrap" height="30" />
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation" style="background-color: #e3f2fd">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse text-center" id="navbarNav">
            <ul class="navbar-nav nav-underline ms-auto me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php if (basename($_SERVER['PHP_SELF']) == 'driver.php') {
                                            echo 'active';
                                        } ?>" aria-current="page" href="driver.php">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <?php
                    // Include the database connection file
                    include '../db/tdbconn.php';


                    // Get the commuter ID from the session
                    $driverID = $_SESSION["driverid"];

                    // Query to select the first name from the database
                    $query = "SELECT firstname FROM driver WHERE driverID = $driverID";
                    $result = mysqli_query($conn, $query);

                    // Check if the query was successful
                    if ($result) {
                        // Fetch the first name from the result
                        $row = mysqli_fetch_assoc($result);
                        $firstName = $row['firstname'];

                        // Display the first name in the navbar
                        echo '<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
                        echo $firstName;
                        echo '</a>';
                    } else {
                        // If the query fails, display a default name
                        echo '<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">';
                        echo 'Guest';
                        echo '</a>';
                    }
                    ?>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="dmanage.php">Manage Account</a></li>
                        <li>
                            <hr class="dropdown-divider" />
                        </li>
                        <li><a class="dropdown-item" href="../php/logout.php" style="color: red;">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>