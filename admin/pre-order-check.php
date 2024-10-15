<?php
session_start(); 
include('../connection.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$success = false; // Initialize the success variable

function sendEmail($email, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();                                 // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';                  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                          // Enable SMTP authentication
        $mail->Username = 'example@gmail.com';           // SMTP username
        $mail->Password = 'password';                    // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
        $mail->Port = 587;                               // TCP port to connect to

        // Recipients
        $mail->setFrom('example@gmail.com', 'name');     // Replace with your email and name
        $mail->addAddress($email);                       // Add a recipient

        // Content
        $mail->isHTML(true);                             // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['order_id'];  // Ensure correct field name
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];

    // Fetch user's email based on user_id
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $email = $stmt->fetchColumn();
    $stmt = null; // Close the statement

    if ($action == 'confirm') {
        $stmt = $conn->prepare("UPDATE pre_order SET status = 'confirmed' WHERE id = ?");
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            // Send confirmation email
            $subject = "Your pre-order has been confirmed!";
            $message = "Dear Customer, your pre-order with ID $id has been confirmed. Thank you!";
            if (sendEmail($email, $subject, $message)) {
                $_SESSION['alert'] = "Pre-order confirmed and email sent successfully.";
            } else {
                $_SESSION['alert'] = "Pre-order confirmed, but failed to send email.";
            }
        } else {
            $_SESSION['alert'] = "Failed to confirm pre-order.";
        }
        $stmt = null; // Close the statement
    } elseif ($action == 'reject') {
        $stmt = $conn->prepare("UPDATE pre_order SET status = 'rejected' WHERE id = ?");
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            // Send rejection email
            $subject = "Your pre-order has been rejected.";
            $message = "Dear Customer, unfortunately, your pre-order with ID $id has been rejected. Please contact us for more details.";
            if (sendEmail($email, $subject, $message)) {
                $_SESSION['alert'] = "Pre-order rejected and email sent successfully.";
            } else {
                $_SESSION['alert'] = "Pre-order rejected, but failed to send email.";
            }
        } else {
            $_SESSION['alert'] = "Failed to reject pre-order.";
        }
        $stmt = null; // Close the statement
    }
    
    // Close the database connection
    $conn = null;

    header("Location: pre-order-check.php"); // Redirect back to the admin page
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin/template-admin.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/admin/add-menu.css">
    <link rel="stylesheet" href="../css/admin/pre-order-check.css">
    <script src="../js/common.js" defer></script>
    <title>Admin</title>
</head>
<body>
    <?php include('nav_admin.php'); ?>
    
    <section class="home-section">
        <div class="home-content">
            <i class="bx bx-menu"></i>
            <span class="text">Pre-Order</span>
        </div>
        <div class="next">
            <div class="card_set2">
                <!-- Card Counts for Pre-Orders -->
                <div class="card2">
                    <div class="header">
                        <i class='bx bxs-check-circle'></i>
                        <h2>Confirmed</h2>
                        <?php
                            $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM pre_order WHERE status = 'confirmed'");
                            $stmt->execute();
                            $count = $stmt->fetchColumn();
                            echo "<span class='counter'>" . $count . "</span>";
                            $stmt = null; // Close statement
                        ?>
                    </div>
                </div>
                <div class="card2">
                    <div class="header">
                        <i class='bx bxs-time-five'></i>
                        <h2>Pending</h2>
                        <?php
                            $stmt = $conn->prepare("SELECT COUNT(*) FROM pre_order WHERE status = 'pending'");
                            $stmt->execute();
                            $count = $stmt->fetchColumn();
                            echo "<span class='counter'>" . $count . "</span>";
                            $stmt = null; // Close statement
                        ?>
                    </div>
                </div>
                <div class="card2">
                    <div class="header">
                        <i class='bx bxs-x-circle'></i>
                        <h2>Rejected</h2>
                        <?php
                            $stmt = $conn->prepare("SELECT COUNT(*) FROM pre_order WHERE status = 'rejected'");
                            $stmt->execute();
                            $count = $stmt->fetchColumn();
                            echo "<span class='counter'>" . $count . "</span>";
                            $stmt = null; // Close statement
                        ?>
                    </div>
                </div>
            </div>

            <div class="pre-order">
                <?php
                // SQL query to fetch pending pre-orders with item names concatenated
                $sql = "SELECT p.id AS order_id, p.user_id, p.visiting_date, 
                            GROUP_CONCAT(CONCAT(m.Item, ' (Qty: ', o.quantity, ')') SEPARATOR '<br>') AS items,
                            SUM(o.price * o.quantity) AS total_price
                        FROM pre_order p
                        INNER JOIN order_items o ON p.id = o.order_id
                        INNER JOIN menu m ON m.id = o.menu_item_id
                        WHERE p.status = 'pending'
                        GROUP BY p.id, p.user_id, p.visiting_date";

                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Check if there are results
                if (count($result) > 0) {
                    echo "<table border='1'>";
                    echo "<tr>
                            <th>User ID</th>
                            <th>Items & Quantity</th>
                            <th>Total Price</th>
                            <th>Visiting Date</th>
                            <th>Action</th>
                        </tr>";

                    foreach ($result as $row) {
                        echo "<tr>";
                        echo "<td>" . $row['user_id'] . "</td>";
                        echo "<td>" . $row['items'] . "</td>";
                        echo "<td>" . $row['total_price'] . "</td>";
                        echo "<td>" . $row['visiting_date'] . "</td>";
                        echo "<td>
                                <form action='pre-order-check.php' method='post'>
                                    <input type='hidden' name='order_id' value='" . $row['order_id'] . "'>
                                    <input type='hidden' name='user_id' value='" . $row['user_id'] . "'>
                                    <input type='hidden' name='visiting_date' value='" . $row['visiting_date'] . "'>
                                    <button type='submit' name='action' value='confirm'>Confirm</button>
                                    <button type='submit' name='action' value='reject'>Reject</button>    
                                </form>
                            </td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "No pending pre-orders.";
                }
                ?>
            </div>
        </div>
    </section>

    <script>
        // Display an alert if it exists
        <?php if (isset($_SESSION['alert'])) { ?>
            alert('<?php echo $_SESSION['alert']; ?>');
            <?php unset($_SESSION['alert']); ?>
        <?php } ?>
    </script>
</body>
</html>
