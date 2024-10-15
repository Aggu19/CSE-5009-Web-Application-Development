<?php
session_start();
include('../connection.php');
$success = false; // Initialize the success variable
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../admin/template-admin.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../admin/bookings.css">
    <link rel="stylesheet" href="../admin/admin-home.css">
    <script src="../js/common.js" defer></script>
    <title>Staff - Reservations</title>
</head>
<body>
    <?php include('nav_staff.php'); ?>
    <section class="home-section">
        <div class="home-content">
            <i class="bx bx-menu"></i>
            <span class="text">Manage Reservation</span>
        </div>
        <div class="next">
            <div class="reservations-table">
            <?php
            // Fetch pending reservations
            $sql = "SELECT * FROM reservations WHERE status = 'pending'";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $pendingReservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($pendingReservations) > 0) {
                echo "<table>";
                echo "<tr><th>Name</th><th>Contact Info</th><th>Number of Guests</th><th>Date</th><th>Time</th><th>Actions</th></tr>";
                foreach ($pendingReservations as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['contact_info']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['num_guests']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['reservation_date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['reservation_time']) . "</td>";
                    echo "<td>
                            <form action='confirm.php' method='post'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                                <select name='table_number'>
                                    <option value=''>Select Table</option>";
                                    $table_sql = "SELECT table_no FROM res_table WHERE status = 'free'";
                                    $table_stmt = $conn->prepare($table_sql);
                                    $table_stmt->execute();
                                    $availableTables = $table_stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    foreach ($availableTables as $table_row) {
                                        echo "<option value='" . htmlspecialchars($table_row['table_no']) . "'>Table " . htmlspecialchars($table_row['table_no']) . "</option>";
                                    }
                    echo "        </select>
                                <button type='submit' name='action' value='confirm'>Confirm</button>
                                <button type='submit' name='action' value='reject'>Reject</button>
                            </form>
                          </td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No pending reservations.";
            }
            ?>
            </div>
            <div class="other">
            <div class="table_info">
                    <?php
                    // Table for all reservation details
                    $stmt_r = $conn->prepare("SELECT * FROM reservations");
                    $stmt_r->execute();
                    $result_r = $stmt_r->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($result_r) > 0) {
                        echo "<table>";
                        echo "<tr>";
                        echo "<th>Table No</th>";
                        echo "<th>No of Guests</th>";
                        echo "<th>Date</th>";
                        echo "<th>Time</th>";
                        echo "<th>Status</th>";
                        echo "</tr>";
                        foreach ($result_r as $row) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['table_number']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['num_guests']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['reservation_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['reservation_time']) . "</td>";
                            // Translate the status value to a user-friendly text
                            $status_text = '';
                            switch ($row['status']) {
                                case 'pending':
                                    $status_text = 'Pending';
                                    break;
                                case 'confirmed':
                                    $status_text = 'Confirmed';
                                    break;
                                case 'rejected':
                                    $status_text = 'Rejected';
                                    break;
                                default:
                                    $status_text = 'Unknown';
                                    break;
                            }
                            echo "<td>" . htmlspecialchars($status_text) . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "No reservations found";
                    }
                    $conn = null; // Close the connection
                    ?>
                </div>
            </div>
        </div>
    </section>
    <script>
        // Check if the session variable 'alert' is set and display the alert
        <?php if (isset($_SESSION['alert'])): ?>
            alert("<?php echo $_SESSION['alert']; ?>");
            <?php unset($_SESSION['alert']); ?> // Clear the session variable after displaying the alert
        <?php endif; ?>
    </script>
</body>
</html>
