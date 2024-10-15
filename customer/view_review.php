<?php
session_start();
include('../connection.php'); // Adjust the path as necessary

$user_id = $_SESSION['user_id'];

// Fetch menu items from the database
$menu_query = "SELECT * FROM `menu`";
$menu_result = $conn->query($menu_query); // Use PDO's query() method

$reviews = [];
$selected_food = '';

if (isset($_GET['food']) && !empty($_GET['food'])) {
    $selected_food = $_GET['food'];

    // Fetch reviews for the selected menu item
    $stmt = $conn->prepare("SELECT user_id, menu_item, review, created_at FROM review WHERE menu_item = ?");
    $stmt->execute([$selected_food]); // Bind the selected menu item
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all reviews as associative arrays
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/template.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/watch.css"> 
    <!-- Include the JavaScript file -->
    <script src="../js/common.js" defer></script>
    <title>Review</title>
</head>
<body>
    <?php include('nav_user.php'); ?>
    
    <section class="home-section">
        <div class="home-content">
            <i class="bx bx-menu"></i>
            <span class="text">Review</span>
        </div>
       
        <div class="next">
            <div class="search-form">
                <form action="" method="GET">
                    <select name="food" id="food">
                        <option value="">Select Menu Item</option>
                        <?php
                        // Populate the dropdown with menu items
                        while($menu_result_row = $menu_result->fetch(PDO::FETCH_ASSOC)){ // Use fetch() with PDO
                            $selected = ($menu_result_row['Item'] === $selected_food) ? 'selected' : '';
                            echo '<option value="'. htmlspecialchars($menu_result_row['Item']). '" '. $selected . '>'. htmlspecialchars($menu_result_row['Item']). '</option>';
                        }
                        ?>
                    </select><br>
                    <input type="submit" value="Filter">
                </form>
            </div>

            <!-- Display reviews for the selected menu item -->
            <div class="reviews">
                <div class="item-box">
                    <h3>Reviews for <?= htmlspecialchars($selected_food) ?></h3>
                </div>
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review">
                            <div class="review-header">
                                <p><strong>Menu Item:</strong> <?= htmlspecialchars($review['menu_item']) ?></p>
                            </div>
                            <div class="review-content">
                                <p><?= htmlspecialchars($review['review']) ?></p>
                            </div>
                            <div class="review-footer">
                                <p><small><strong>Date:</strong> <?= htmlspecialchars($review['created_at']) ?></small></p>
                                <p><small><strong>User:</strong> <?= htmlspecialchars($review['user_id']) ?></small></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No reviews available for this menu item.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

</body>
</html>
