<?php
session_start();
include('../connection.php');
$success = false; // Initialize the success variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $event_description = $_POST['event_description'];

    // Insert the data into the database
    $stmt = $conn->prepare("INSERT INTO events (e_name, start_date, end_date, e_description) VALUES (:event_name, :start_date, :end_date, :event_description)");

    // Bind parameters using bindValue
    $stmt->bindValue(':event_name', $event_name);
    $stmt->bindValue(':start_date', $start_date);
    $stmt->bindValue(':end_date', $end_date);
    $stmt->bindValue(':event_description', $event_description);

    // Execute the statement
    $stmt->execute();

    // Check if the data was inserted successfully
    if ($stmt->rowCount() > 0) {
        $success = true;
        // Set a session variable for displaying the alert
        $_SESSION['alert'] = "Event added successfully!";
        // Redirect to the same page to prevent form resubmission
        header("Location: add_events.php");
        exit();
    } else {
        echo "Error adding menu item: " . $stmt->errorInfo()[2]; // More detailed error message
    }
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
    <link rel="stylesheet" href="../css/admin/add-specialEvents.css">
    <script src="../js/common.js" defer></script>
    <title>Admin-Add Events</title>
</head>
<body>
    <?php include('nav_admin.php'); ?>
    
    <section class="home-section">
        <div class="home-content">
            <i class="bx bx-menu"></i>
            <span class="text">Add Events</span>
        </div>
        <div class="next">
            <form action="add_events.php" method="post" enctype="multipart/form-data" class="form-Event">
                <label for="event_name">Event Name:</label>
                <input type="text" id="event_name" name="event_name" required><br><br>

                <label for="event_description">Description:</label>
                <textarea id="event_description" name="event_description" required></textarea><br><br>
                
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" required><br><br>

                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" required><br><br>
           
                <input type="submit" value="Add Event">
            </form>
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
