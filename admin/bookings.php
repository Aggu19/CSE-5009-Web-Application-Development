<?php
session_start(); // Start the session to use session variables
include('../connection.php'); // Adjust this path if necessary

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$success = false;

// Remove the connect_error check for PDO
// Use try-catch for exception handling if necessary
try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data
        $id = $_POST['id'];
        $action = $_POST['action'];
        $table_number = $_POST['table_number'];

        if ($action == 'confirm') {
            $status = 'confirmed';
        } else {
            $status = 'rejected';
            $table_number = NULL;
        }

        // Update the reservation status in the database
        $stmt = $conn->prepare("UPDATE reservations SET status = ?, table_number = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $table_number, $id]);

        if ($stmt->rowCount() > 0) { // Use rowCount() to check affected rows
            if ($status == 'confirmed') {
                $table_stmt = $conn->prepare("UPDATE res_table SET status = 'booked' WHERE table_no = ?");
                $table_stmt->execute([$table_number]);
            }

            // Fetch the user email and name
            $email_stmt = $conn->prepare("SELECT email, name FROM reservations WHERE id = ?");
            $email_stmt->execute([$id]);
            $user = $email_stmt->fetch(PDO::FETCH_ASSOC);
            $user_email = $user['email'];
            $user_name = $user['name'];

            // Send the email (implement PHPMailer settings)

            $success = true;
            $_SESSION['alert'] = "Reservation Updated successfully!";
            header("Location: bookings.php");
            exit();
        } else {
            echo "Error: " . $stmt->errorInfo()[2]; // Use errorInfo() for PDO errors
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin/template-admin.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/admin/bookings.css">
    <script src="../js/common.js" defer></script>
    <title>Admin Manage Bookings</title>
</head>
<body>
    <?php include('nav_admin.php'); ?>
    
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
            $stmt = $conn->query($sql);

            if ($stmt->rowCount() > 0) { // Use rowCount() instead of num_rows
                echo "<table>";
                echo "<tr><th>Name</th><th>Contact Info</th><th>Number of Guests</th><th>Date</th><th>Time</th><th>Actions</th></tr>";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['contact_info'] . "</td>";
                    echo "<td>" . $row['num_guests'] . "</td>";
                    echo "<td>" . $row['reservation_date'] . "</td>";
                    echo "<td>" . $row['reservation_time'] . "</td>";
                    echo "<td>
                            <form action='bookings.php' method='post'>
                                <input type='hidden' name='id' value='" . $row['id'] . "'>
                                <select name='table_number'>
                                    <option value=''>Select Table</option>";
                                    $table_sql = "SELECT table_no, capacity FROM res_table WHERE status = 'free'";
                                    $table_stmt = $conn->query($table_sql);
                                    while ($table_row = $table_stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='" . $table_row['table_no'] . "'>Table " . $table_row['table_no'] . " (capacity: " . $table_row['capacity'] . ")</option>";
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
