<?php
include('../connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/template.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/special-events.css"> <!-- Ensure your menu-specific CSS is linked -->
    <script src="../js/common.js" defer></script>
    <title>Special</title>
</head>
<body>
    <?php include('nav_user.php'); ?>
    
    <section class="home-section">
        <div class="home-content">
            <i class="bx bx-menu"></i>
            <span class="text">Special Events</span>
        </div>
       
        <div class="next">
        <div class="cards">
            <!-- //selecting event items -->
                <?php
                    // Use PDO to fetch event data
                    $sql = "SELECT * FROM events";
                    $stmt = $conn->query($sql); // PDO's query() method
                    $events = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all records

                    if (count($events) > 0) {
                        foreach ($events as $event) {
                            echo '<div class="card">';
                            // Display event item details as cards
                            echo '<div class="card-content">';
                            echo '<div class="card-header">';
                            echo '<h3>' . htmlspecialchars($event['e_name']) . '</h3>';
                            echo '</div>';
                            echo '<div class="description">';
                            echo '<p>' . htmlspecialchars($event['e_description']) . '</p>';
                            echo '</div>';
                            echo '<div class="card-footer">';
                            echo '<p> From: ' . htmlspecialchars($event['start_date']) . '</p>';
                            echo '<p> To:   ' . htmlspecialchars($event['end_date']) . '</p>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No events found</p>';
                    }
                ?>
        </div>
    </div>
</section>

</body>
</html>
