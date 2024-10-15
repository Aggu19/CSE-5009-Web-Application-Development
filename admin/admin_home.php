<?php
session_start();
include('../connection.php');
$success = false;

// For order counts
$query = $conn->query("
    SELECT 
        COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as c_orders,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as p_orders,
        COUNT(CASE WHEN status = 'rejected' THEN 1 END) as r_orders
    FROM pre_order
");

$c_orders = $p_orders = $r_orders = 0;
if ($query) {
    $data = $query->fetch(PDO::FETCH_ASSOC);
    $c_orders = $data['c_orders'];
    $p_orders = $data['p_orders'];
    $r_orders = $data['r_orders'];
} else {
    echo "Error: " . $conn->error;
}

$order_counts = json_encode([$c_orders, $p_orders, $r_orders]);

// For reservation data
$query = $conn->query("
    SELECT 
        COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as c_res,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as p_res,
        COUNT(CASE WHEN status = 'rejected' THEN 1 END) as r_res
    FROM reservations
");

$c_res = $p_res = $r_res = 0;
if ($query) {
    $data = $query->fetch(PDO::FETCH_ASSOC);
    $c_res = $data['c_res'];
    $p_res = $data['p_res'];
    $r_res = $data['r_res'];
} else {
    echo "Error: " . $conn->error;
}

$reservation_data = json_encode([$c_res, $p_res, $r_res]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script type="text/javascript">
        function preventBack() {
            window.history.forward();
        }
        setTimeout("preventBack()", 0);
        window.onunload = function() {null;}
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/template.css">
    <link rel="stylesheet" href="../css/admin/admin-home.css">
    <link rel="stylesheet" href="../css/home.css">
</head>
<body>
<?php include('nav_admin.php'); ?>

<section class="home-section">
    <div class="home-content">
        <i class="bx bx-menu"></i>
        <span class="text">Welcome </span>
    </div>

    <div class="next">
        <!-- Main card set -->
        <div class="card_set">
            <div class="card">
                <div class="header">
                    <i class='bx bxs-user-circle'></i>
                    <h2>No of Users</h2>
                    <?php
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM users");
                    $stmt->execute();
                    $count = $stmt->fetchColumn();
                    echo "<span class='counter'>" . $count . "</span>";
                    $stmt->closeCursor();
                    ?>
                </div>
            </div>
            <div class="card">
                <div class="header">
                    <i class='bx bxs-food-menu'></i>
                    <h2>Menu Items</h2>
                    <?php
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM menu");
                    $stmt->execute();
                    $count = $stmt->fetchColumn();
                    echo "<span class='counter'>" . $count . "</span>";
                    $stmt->closeCursor();
                    ?>
                </div>
            </div>
            <div class="card">
                <div class="header">
                    <i class='bx bxs-cube-alt'></i>
                    <h2>No of Tables</h2>
                    <?php
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM res_table");
                    $stmt->execute();
                    $count = $stmt->fetchColumn();
                    echo "<span class='counter'>" . $count . "</span>";
                    $stmt->closeCursor();
                    ?>
                </div>
            </div>
        </div>

        <!-- Graphs -->
        <div class="graphbox">
            <div class="box">
                <canvas id="myChart" width></canvas>
            </div>
            <div class="box">
                <canvas id="myChartReservation" width></canvas>
            </div>
        </div>

        <!-- Table for all reservation details -->
        <div class="other">
            <div class="table_info">
                <?php
                $stmt = $conn->prepare("SELECT * FROM reservations");
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($result) {
                    echo "<table>";
                    echo "<tr><th>Table No</th><th>No of Guests</th><th>Date</th><th>Time</th><th>Status</th></tr>";
                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>" . $row['table_number'] . "</td>";
                        echo "<td>" . $row['num_guests'] . "</td>";
                        echo "<td>" . $row['reservation_date'] . "</td>";
                        echo "<td>" . $row['reservation_time'] . "</td>";
                        $status_text = ucfirst($row['status']);
                        echo "<td>" . $status_text . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "No reservations found";
                }
                ?>
            </div>

            <!-- Reservation status cards -->
            <div class="card_set2">
                <div class="card2">
                    <div class="header">
                        <i class='bx bxs-check-circle'></i>
                        <h2>Confirmed Reservations</h2>
                        <?php
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE status = 'confirmed'");
                        $stmt->execute();
                        $count = $stmt->fetchColumn();
                        echo "<span class='counter'>" . $count . "</span>";
                        $stmt->closeCursor();
                        ?>
                    </div>
                </div>
                <div class="card2">
                    <div class="header">
                        <i class='bx bxs-time-five'></i>
                        <h2>Pending Reservations</h2>
                        <?php
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE status = 'pending'");
                        $stmt->execute();
                        $count = $stmt->fetchColumn();
                        echo "<span class='counter'>" . $count . "</span>";
                        $stmt->closeCursor();
                        ?>
                    </div>
                </div>
                <div class="card2">
                    <div class="header">
                        <i class='bx bxs-x-circle'></i>
                        <h2>Rejected Reservations</h2>
                        <?php
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM reservations WHERE status = 'rejected'");
                        $stmt->execute();
                        $count = $stmt->fetchColumn();
                        echo "<span class='counter'>" . $count . "</span>";
                        $stmt->closeCursor();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</body>
<script src="../js/common.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Charts -->
<script>
    // Chart for Orders
    var orderCounts = <?php echo $order_counts; ?>;
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'polarArea',
        data: {
            labels: ['Confirmed', 'Pending', 'Rejected'],
            datasets: [{
                label: 'Order Status',
                data: orderCounts,
                backgroundColor: ['rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });

    // Chart for Reservations
    var reservationCounts = <?php echo $reservation_data; ?>;
    var ctx = document.getElementById('myChartReservation').getContext('2d');
    var myChartReservation = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Confirmed', 'Pending', 'Rejected'],
            datasets: [{
                label: 'Reservation Status',
                data: reservationCounts,
                backgroundColor: ['rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(255, 99, 132, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
</html>
