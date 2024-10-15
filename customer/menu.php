<?php
session_start();
include('../connection.php'); // Ensure this includes your PDO connection setup

// Check connection (optional, PDO throws exceptions)
try {
    // Fetch menu items from the database
    $query = "SELECT * FROM `menu`";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// Search functionality
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT * FROM `menu` WHERE `Item` LIKE :search";
    $stmt = $conn->prepare($query);
    $stmt->execute(['search' => '%' . $search . '%']);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Filter by cuisine
if (isset($_GET['cuisine'])) {
    $cuisine = $_GET['cuisine'];
    $query = "SELECT * FROM `menu` WHERE `Cuisine` = :cuisine";
    $stmt = $conn->prepare($query);
    $stmt->execute(['cuisine' => $cuisine]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Define the ENUM values for cuisines
$cuisineOptions = ['Sri Lankan', 'Chinese', 'Italian', 'Indian'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/template.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/menu.css"> <!-- Ensure your menu-specific CSS is linked -->
    <script src="../js/common.js" defer></script>
    <title>Menu</title>
</head>
<body>
    <?php include('nav_user.php'); ?>
    
    <section class="home-section">
        <div class="home-content">
            <i class="bx bx-menu"></i>
            <span class="text">Menu</span>
        </div>
       
        <div class="next">
            <div class="mainform">
                <div class="search-form">
                    <form action="" method="GET">
                        <input type="text" name="search" placeholder="Search by item name...">
                        <input type="submit" value="Search">
                    </form>
                </div>
                <div class="cuisine-form">
                    <form action="" method="GET">
                        <select name="cuisine" class="cuisine-select">
                            <option value="">Select Cuisine</option>
                            <?php
                            // Populate the dropdown with ENUM values
                            foreach ($cuisineOptions as $option) {
                                echo '<option value="' . htmlspecialchars($option) . '">' . htmlspecialchars($option) . '</option>';
                            }
                            ?>
                        </select>
                        <input type="submit" value="Filter">
                    </form>
                </div>
            </div>
            
            <div class="cards">
            <?php
            // Check if there are any results
            if (!empty($result)) {
                // Output data of each row
                foreach ($result as $row) {
                    // Display menu item details as cards
                    echo '<div class="card">';
                    // Display image if available
                    if (!empty($row['img_dr'])) {
                        // Extract relative path from full image path
                        $relative_path = substr($row['img_dr'], strpos($row['img_dr'], '\main-folder'));
                        echo '<img src="' . htmlspecialchars($relative_path) . '" alt="' . htmlspecialchars($row['Item']) . '">';
                    }
                    echo '<h3>' . htmlspecialchars($row['Item']) . '</h3>';
                    echo '<p>Cuisine: ' . htmlspecialchars($row['Cuisine']) . '</p>';
                    echo '<p>Price: Rs ' . htmlspecialchars($row['Price']) . '</p>';
                    echo '<p>' . htmlspecialchars($row['Description']) . '</p>';
                    echo '</div>';
                }
            } else {
                echo '<p>No menu items found.</p>';
            }
            ?>
            </div>
            
        </div>
    </section>
    <script>
        // Pre-select cuisine option if it's in the query string
        var urlParams = new URLSearchParams(window.location.search);
        var cuisineParam = urlParams.get('cuisine');
        if (cuisineParam) {
            var select = document.querySelector('.cuisine-select');
            select.value = cuisineParam;
        }
    </script>
</body>
</html>
