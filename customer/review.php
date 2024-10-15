<?php
session_start();
include('../connection.php'); // Adjust the path as necessary

$user_id = $_SESSION['user_id'];
$error = '';

// Handle review submission
if (isset($_POST['submit'])) {
    $menu_item = $_POST['food'];
    $review = $_POST['review'];

    if (!empty($menu_item) && !empty($review)) {
        $stmt = $conn->prepare("INSERT INTO review (user_id, menu_item, review) VALUES (:user_id, :menu_item, :review)");
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':menu_item', $menu_item, PDO::PARAM_STR);
        $stmt->bindValue(':review', $review, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            $_SESSION['alert'] = "Review added successfully!";
            header("Location: review.php");
            exit();
        } else {
            $error = "Error adding review. Please try again.";
        }
        
        $stmt = null;
    } else {
        $error = "Please fill in all fields.";
    }
}

// Handle review deletion
if (isset($_POST['delete'])) {
    $review_id = $_POST['review_id'];

    $stmt = $conn->prepare("DELETE FROM review WHERE id = :review_id AND user_id = :user_id");
    $stmt->bindValue(':review_id', $review_id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        $_SESSION['alert'] = "Review deleted successfully!";
        header("Location: review.php");
        exit();
    } else {
        $error = "Error deleting review. Please try again.";
    }
    
    $stmt = null;
}

// Fetch reviews from the database
$query = "SELECT id, menu_item, review, created_at FROM review WHERE user_id = :user_id";
$stmt = $conn->prepare($query);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/template.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/c-review.css"> <!-- Ensure your menu-specific CSS is linked -->
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
        <div class="form_data">
                <form action="review.php" method="post" enctype="multipart/form-data" class="form-book">
                    <div class="form-row">
                        <select name="food" id="food">
                            <option value="">Select Menu Item</option>
                            <?php
                            // Fetch menu items from the database
                            $query = "SELECT * FROM `menu`";
                            $result = $conn->query($query); // Use PDO's query() method
                            while($menu_result_row = $result->fetch(PDO::FETCH_ASSOC)){
                                echo '<option value="'. $menu_result_row['Item']. '">'. $menu_result_row['Item']. '</option>';
                            }
                            ?>
                        </select><br>
                        <label for="review">Review:</label>
                        <textarea name="review" id="review" required></textarea>
                    
                        <div class="btn_box">
                        <input type="submit" name="submit" value="Add Review">
                        <input type="reset" class="btn1" name="cancel" value="Cancel"> 
                        </div>
                    </div>   
                </form>
                <?php
                if (isset($error)) {
                    echo "<p>$error</p>";
                }
                ?>
            </div>

            <!-- Display user reviews -->
            <div class="reviews">
                <h3>Your Reviews</h3>
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review">
                            <p><strong>Menu Item:</strong> <?= htmlspecialchars($review['menu_item']) ?></p>
                            <p><strong>Review:</strong> <?= htmlspecialchars($review['review']) ?></p>
                            <p><small><strong>Date:</strong> <?= htmlspecialchars($review['created_at']) ?></small></p>
                            <form action="review.php" method="post" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                <input type="submit" name="delete" value="Delete" class="btn-delete">
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>You haven't added any reviews yet.</p>
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
