<?php
session_start();
include('../connection.php'); // Assuming $conn is a PDO instance

// Fetch menu items from the database using PDO
$menu_query = "SELECT * FROM `menu`";
$menu_result = $conn->query($menu_query); // Use PDO's query method

$reviews = [];
$selected_food = '';

if (isset($_GET['food']) && !empty($_GET['food'])) {
    $selected_food = $_GET['food'];

    // Fetch reviews for the selected menu item using PDO
    $stmt = $conn->prepare("SELECT id, user_id, menu_item, review, created_at FROM review WHERE menu_item = ?");
    $stmt->execute([$selected_food]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all reviews as an associative array
}

// Handle review deletion
if (isset($_POST['delete'])) {
    $review_id = $_POST['review_id'];

    // Use PDO to delete the review
    $stmt = $conn->prepare("DELETE FROM review WHERE id = ?");
    
    if ($stmt->execute([$review_id])) {
        $_SESSION['alert'] = "Review deleted successfully!";
        header("Location: manage-cr.php");
        exit();
    } else {
        $error = "Error deleting review. Please try again.";
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
    <link rel="stylesheet" href="../css/admin/manage-cr.css">
    <script src="../js/common.js" defer></script>
    <title>Admin</title>
</head>
<body>
    <?php include('nav_admin.php'); ?>
    <section class="home-section">
        <div class="home-content">
            <i class="bx bx-menu"></i>
            <span class="text">Manage Review</span>
        </div>
        <div class="next">
            <div class="search-form">
                <form action="" method="GET">
                    <select name="food" id="food">
                        <option value="">Select Menu Item</option>
                        <?php
                        // Populate the dropdown with menu items
                        while($menu_result_row = $menu_result->fetch(PDO::FETCH_ASSOC)){
                            $selected = ($menu_result_row['Item'] === $selected_food) ? 'selected' : '';
                            echo '<option value="'. $menu_result_row['Item']. '" '. $selected . '>'. $menu_result_row['Item']. '</option>';
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
                                <form action="manage-cr.php" method="post" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                    <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                    <input type="submit" name="delete" value="Delete" class="btn-delete">
                                </form>
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
