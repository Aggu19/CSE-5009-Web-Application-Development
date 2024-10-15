<?php
session_start();
include('connection.php');

// Initialize variables
$success = false;
$error = '';

// Check if user is logged in and session is set
if (!isset($_SESSION['user_id'])) {
    $error = 'User not logged in.';
    header('Location: login.php'); // Redirect to login if session not set
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and validate form inputs
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $dob = trim($_POST['dob']);

    // Check if any required fields are empty
    if (empty($name) || empty($address) || empty($phone) || empty($dob)) {
        $error = 'All fields are required!';
    } else {
        // Prepare the SQL statement to update the user details
        $sql = "UPDATE user_details SET name = ?, address = ?, phone = ?, dob = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Bind the parameters (s for strings, i for integer)
            $stmt->bind_param("ssssi", $name, $address, $phone, $dob, $user_id);

            // Execute the update query
            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = 'Failed to update data: ' . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            $error = 'Failed to prepare the statement: ' . $conn->error;
        }
    }
}

// Redirect to the display page if the update is successful
if ($success) {
    header('Location: edit_user.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Details</title>
</head>
<body>
    <h2>Edit Your Details</h2>
    
    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>

        <label for="address">Address:</label>
        <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($address ?? ''); ?>" required>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>" required>

        <label for="dob">Date of Birth:</label>
        <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($dob ?? ''); ?>" required>

        <button type="submit">Update</button>
    </form>
</body>
</html>
