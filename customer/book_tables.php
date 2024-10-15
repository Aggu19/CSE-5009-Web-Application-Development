<?php
session_start();
include('../connection.php'); // Include the database connection

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST['name'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $table_id = $_POST['table_id'] ?? '';

    // Validate form data
    if (!empty($name) && !empty($contact_number) && !empty($table_id)) {
        try {
            // Prepare the SQL statement for inserting a reservation
            $stmt = $conn->prepare("INSERT INTO reservations (name, contact_info, table_id, status) VALUES (:name, :contact_number, :table_id, 'pending')");
            // Execute the statement with user input
            $stmt->execute(['name' => $name, 'contact_number' => $contact_number, 'table_id' => $table_id]);

            // Mark the table as booked
            $update_stmt = $conn->prepare("UPDATE tables SET is_booked = 1 WHERE id = :table_id");
            $update_stmt->execute(['table_id' => $table_id]);

            echo "<p>Table booked successfully!</p>";
        } catch (PDOException $e) {
            echo "Booking failed: " . $e->getMessage();
        }
    } else {
        echo "<p>Please fill in all fields.</p>";
    }
}

// Fetch available tables from the database
try {
    $query = "SELECT * FROM tables WHERE is_booked = 0"; // Fetch available tables
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $available_tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching tables: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/template.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/book_tables.css"> <!-- Add your own styles -->
    <title>Book a Table</title>
</head>
<body>
    <?php include('nav_user.php'); ?>

    <section class="home-section">
        <div class="home-content">
            <h1>Book a Table</h1>
        </div>

        <div class="booking-form">
            <form action="" method="POST">
                <label for="name">Your Name:</label>
                <input type="text" name="name" required>

                <label for="contact_number">Contact Number:</label>
                <input type="text" name="contact_number" required>

                <label for="table_id">Select Table:</label>
                <select name="table_id" required>
                    <option value="">Select a table</option>
                    <?php foreach ($available_tables as $table): ?>
                        <option value="<?php echo htmlspecialchars($table['id']); ?>">
                            Table <?php echo htmlspecialchars($table['id']); ?> - Seats: <?php echo htmlspecialchars($table['seats']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="submit" value="Book Table">
            </form>
        </div>

        <?php
        // Optionally, display all reservations
        try {
            $booking_query = "SELECT * FROM reservations"; // Changed from bookings to reservations
            $booking_stmt = $conn->prepare($booking_query);
            $booking_stmt->execute();
            $bookings = $booking_stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($bookings) > 0) {
                echo "<h2>Current Bookings:</h2>";
                foreach ($bookings as $booking) {
                    echo "<p>Name: " . htmlspecialchars($booking['name']) . ", Contact: " . htmlspecialchars($booking['contact_info']) . ", Table ID: " . htmlspecialchars($booking['table_id']) . ", Status: " . htmlspecialchars($booking['status']) . "</p>";
                }
            } else {
                echo "<p>No current bookings.</p>";
            }
        } catch (PDOException $e) {
            echo "Error fetching bookings: " . $e->getMessage();
        }
        ?>
    </section>
</body>
</html>
