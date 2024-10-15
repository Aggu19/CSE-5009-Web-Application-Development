<?php
// Start the session at the beginning of the file
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satff Dashboard</title>
    <link rel="stylesheet" href="../css/template.css">
    <link rel="stylesheet" href="../css/home.css">
</head>
<body>
<?php include('nav_staff.php'); ?>

<section class="home-section">
    <div class="home-content">
        <i class="bx bx-menu"></i>
        
        <!-- Check if $_SESSION['username'] is set to avoid errors -->
        <span class="text">
            Welcome 
            <?php
            if (isset($_SESSION["username"])) {
                echo htmlspecialchars($_SESSION["username"]);
            } else {
                echo "Guest"; // Fallback if no session is set
            }
            ?>
        </span>
    </div>
</section>

</body>
<script src="../js/common.js"></script>
</html>
