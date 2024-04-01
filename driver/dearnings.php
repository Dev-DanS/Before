<?php
include('../php/session_driver.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver | Earnings</title>
    <link rel="icon" href="../img/logo3.png" type="image/png" />
    <link rel="stylesheet" href="../css/dearnings.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/965a209c77.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
label{
    color: white !important;
}
    </style>
</head>

<body>
    <?php
    include 'dnav.php';
    ?>

<?php
include('../db/tdbconn.php');
$plateNumber = $_SESSION["plateNumber"];
$interval = isset($_GET['interval']) ? $_GET['interval'] : 'day';

switch ($interval) {
    case 'day':
        $intervalClause = "DATE(bookCompletion)";
        break;
    case 'week':
        $intervalClause = "CONCAT(YEAR(bookCompletion), '-', LPAD(WEEK(bookCompletion, 1), 2, '0'))";
        break;
    case 'month':
        $intervalClause = "DATE_FORMAT(bookCompletion, '%Y-%m')";
        break;
    default:
        $intervalClause = "DATE(bookCompletion)";
        break;
}

$sql = "SELECT $intervalClause AS intervalDate, SUM(fare) AS totalFare FROM booking WHERE platenumber = ? AND status = 'completed' AND bookCompletion IS NOT NULL AND bookCompletion != '' GROUP BY intervalDate";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $plateNumber);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[$row['intervalDate']] = $row['totalFare'];
}

$stmt->close();
$conn->close();
?>

<div>
<label for="interval" style="color: white;">Select Interval:</label>
<select id="interval" name="interval" onchange="updateChart(this.value)" style="border-radius: 15px;">
    <option value="day" <?php if ($interval == 'day') echo 'selected'; ?>>Day</option>
    <option value="week" <?php if ($interval == 'week') echo 'selected'; ?>>Week</option>
    <option value="month" <?php if ($interval == 'month') echo 'selected'; ?>>Month</option>
</select>

</div>

<div class="chart">
    <canvas id="myChart" width="400" height="400"></canvas>
</div>

<script>
    function updateChart(interval) {
        window.location.href = 'dearnings.php?interval=' + interval;
    }

    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($data)); ?>,
            datasets: [{
                label: 'Total Fare',
                data: <?php echo json_encode(array_values($data)); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>









</body>

</html>