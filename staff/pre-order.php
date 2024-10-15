<?php
session_start();
include('../connection.php');
$success = false; // Initialize the success variable

// Retrieve pending orders using PDO
$stmt_order = $conn->prepare("SELECT * FROM pre_order WHERE status = 'pending'");
$stmt_order->execute();
$pending_orders = $stmt_order->fetchAll(PDO::FETCH_ASSOC); // Use fetchAll() to get results
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../admin/template-admin.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/pre-order.css">
    <script src="../js/common.js" defer></script>
    <title>Staff - Pre Orders</title>
</head>
<body>
    <?php include('nav_staff.php');?>
    <section class="home-section">
        <div class="home-content">
            <i class="bx bx-menu"></i>
            <span class="text">Manage Pre-Order</span>
        </div>
        <div class="next">
            <div class="pre-order">
                <h2>Pending Pre-Orders</h2>
                <?php
                // Display pending pre-orders in a table
                if (count($pending_orders) > 0) {
                    echo "<table>";
                    echo "<tr>
                        <th>Id</th>
                        <th>User_Id</th>
                        <th>Visiting Date</th>
                        <th>Action</th>
                        </tr>";

                    foreach ($pending_orders as $row) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['visiting_date']) . "</td>";
                        echo "<td>
                                <form action='pre-order-check-opr.php' method='post'>
                                    <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                                    <input type='hidden' name='user_id' value='" . htmlspecialchars($row['user_id']) . "'>
                                    <button type='submit' name='action' value='confirm'>Confirm</button>
                                    <button type='submit' name='action' value='reject'>Reject</button>    
                                </form>
                            </td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "No pending Pre-Orders.";
                }
                ?>
            </div> 
            <div class="pending_orders">
                <!-- Display pending orders in customer page -->
                <?php if (count($pending_orders) > 0): ?>
                    <div class="cards">
                        <?php foreach ($pending_orders as $order): ?>
                            <div class="card">
                                <p>Details: 
                                    <?php
                                    // Select the order items based on order id
                                    $order_id = $order['id'];
                                    $stmt_items = $conn->prepare("SELECT oi.quantity, oi.price, m.Item FROM order_items oi JOIN menu m ON oi.menu_item_id = m.id WHERE oi.order_id = ?");
                                    $stmt_items->bindParam(1, $order_id); // Use bindParam for PDO
                                    $stmt_items->execute();
                                    $order_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC); // Use fetchAll() to get results

                                    $total_amount = 0;
                                    foreach ($order_items as $item) {
                                        $item_name = $item['Item'];
                                        $quantity = $item['quantity'];
                                        $price = $item['price'];
                                        $total_price = $price * $quantity;
                                        $total_amount += $total_price;

                                        echo "$item_name (Quantity: $quantity, Price: $$price, Total: $$total_price)<br>";
                                    }
                                    ?>
                                </p>
                                <p>Order ID: <?= 'GC_ON' . $order['id'] ?> </p>
                                <p>Visiting Date: <?= htmlspecialchars($order['visiting_date']) ?></p>
                                <p id='status'>Order Status: <?= htmlspecialchars($order['status']) ?></p>
                                <p>Total Amount: $<?= $total_amount ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No pending orders found.</p>
                <?php endif; ?>
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
