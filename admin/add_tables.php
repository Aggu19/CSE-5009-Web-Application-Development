<?php
session_start();
include('../connection.php'); // Make sure this file returns a PDO connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data to prevent SQL injection
    $table_no = htmlspecialchars($_POST['table_no']);
    $capacity = htmlspecialchars($_POST['capacity']); // Renamed num_guests to capacity for consistency

    try {
        // Insert into res_table
        $stmt1 = $conn->prepare("INSERT INTO `res_table` (`table_no`, `num_guests`) VALUES (?, ?)");
        $stmt1->execute([$table_no, $capacity]);

        // Also insert into the `tables` table so customers can book it
        $stmt2 = $conn->prepare("INSERT INTO `tables` (`table_no`, `seats`, `is_booked`) VALUES (?, ?, 0)");
        $stmt2->execute([$table_no, $capacity]);

        // Check if both inserts were successful
        if ($stmt1->rowCount() > 0 && $stmt2->rowCount() > 0) {
            $_SESSION['alert'] = 'Table added successfully!';
        } else {
            $_SESSION['alert'] = 'Error adding table!';
        }
    } catch (PDOException $e) {
        // Capture any SQL errors
        $_SESSION['alert'] = 'Database error: ' . $e->getMessage();
    }

    header("Location: add_tables.php"); // Redirect to avoid form resubmission
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
    <link rel="stylesheet" href="../css/admin/reservation.css">
    <script src="../js/common.js" defer></script>
    <title>Admin - Add Tables</title>
</head>
<body>
    <?php include('nav_admin.php'); ?>
    
    <section class="home-section">
        <div class="home-content">
            <i class="bx bx-menu"></i>
            <span class="text">Add Tables</span>
        </div>
        <div class="next">
            <div class="table_container">
                <form action="add_tables.php" method="post" enctype="multipart/form-data" class="form_table">
                    <label for="table_no">Table No:</label>
                    <input type="text" id="table_no" name="table_no" required><br><br>
                    <label for="capacity">Capacity:</label>
                    <input type="text" id="capacity" name="capacity" required><br><br>
                    <input type="submit" value="Add">
                    <input type="reset" value="Reset">
                </form>
            </div>
            
            <div class="display_tables">
                <div class="cards-container">
                    <div class="cards">
                        <?php
                        // Select all records from res_table using PDO
                        $sql = "SELECT * FROM `res_table`";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (count($results) > 0) {
                            foreach ($results as $fetch) {
                                echo '<div class="card">';
                                echo '<div class="card-content">';
                                echo '<h3> No: ' . htmlspecialchars($fetch['table_no']) . '</h3>';
                                echo '<p> Capacity: ' . htmlspecialchars($fetch['num_guests']) . '</p>';
                                echo '</div>';
                                echo '<input type="delete" class="btn" value="Delete" onclick="location.href=\'delete_table.php?id=' . $fetch['id'] . '\'">';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>No tables found</p>';
                        }
                        ?>
                    </div>
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
