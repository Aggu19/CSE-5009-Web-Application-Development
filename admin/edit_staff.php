<?php
session_start();
include('../connection.php'); // Adjust the path as necessary

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Retrieve the staff member's details from the database
    $query = "SELECT * FROM staff WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]); // Execute with the parameter directly
    $staff = $stmt->fetch(PDO::FETCH_ASSOC); // Use fetch() instead of fetch_assoc()

    if (isset($_POST['update_staff'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Update the staff member's details in the database
        $query = "UPDATE staff SET username = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$username, $password, $id]); // Execute with the parameters directly

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Staff updated successfully!');</script>";
        } else {
            echo "<script>alert('Failed to update staff!');</script>";
        }
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
    <link rel="stylesheet" href="../css/admin/add-staff.css">
    <script src="../js/common.js" defer></script>
    <title>Admin - Edit Staff</title>
</head>
<body>
    <?php include('nav_admin.php'); ?>
    <section class="home-section">
        <div class="home-content">
            <i class="bx bx-menu"></i>
            <span class="text">Edit Staff</span>
        </div>
        <div class="next">
            <form action="" method="post" class="form-staff">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($staff['username']); ?>" required><br><br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br><br>
                <input type="submit" name="update_staff" value="Update Staff">
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
