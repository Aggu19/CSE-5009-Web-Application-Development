<?php
session_start(); // Start the session to use session variables
include('../connection.php'); // Adjust this path if necessary

if (isset($_GET['id'])) {
    $id = $_GET['id']; // Get the ID from the URL without escaping yet

    // Prepare the SQL query to delete the menu item
    $sql = "DELETE FROM `menu` WHERE `id` = :id"; // Use a placeholder for the ID
    $stmt = $conn->prepare($sql); // Prepare the statement

    // Bind the parameter using bindValue() for PDO
    $stmt->bindValue(':id', $id, PDO::PARAM_INT); // Bind the ID parameter

    // Execute the prepared statement
    if ($stmt->execute()) {
        $_SESSION['alert'] = 'Menu item deleted successfully!';
    } else {
        $_SESSION['alert'] = 'Error deleting menu item!';
    }
} else {
    $_SESSION['alert'] = 'Error Occurred!';
}

// Redirect back to the previous page
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?>
