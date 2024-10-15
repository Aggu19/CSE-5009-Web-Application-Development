<?php
session_start();
include('connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Check if the user exists in the users table
$sql = "SELECT * FROM users WHERE id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch current user data from users table
} else {
    echo "No user found with the given user ID.";
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data and sanitize
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $dob = trim($_POST['dob']);

    // Check if required fields are filled
    if (!empty($name) && !empty($address) && !empty($phone) && !empty($dob)) {
        // Query user_details table to check if the user has details already
        $details_sql = "SELECT * FROM user_details WHERE user_id = :user_id";
        $details_stmt = $conn->prepare($details_sql);
        $details_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $details_stmt->execute();
        
        // If user details exist, update them; otherwise, insert new details
        if ($details_stmt->rowCount() > 0) {
            $current_details = $details_stmt->fetch(PDO::FETCH_ASSOC);
            
            // Prepare and execute the SQL UPDATE statement for user_details
            $update_sql = "UPDATE user_details SET name = :name, address = :address, phone = :phone, dob = :dob WHERE user_id = :user_id";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $update_stmt->bindValue(':address', $address, PDO::PARAM_STR);
            $update_stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
            $update_stmt->bindValue(':dob', $dob, PDO::PARAM_STR);
            $update_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            
            // Execute the query
            if ($update_stmt->execute()) {
                if ($update_stmt->rowCount() > 0) {
                    echo "Record updated successfully!";
                    header('Location: edit_user.php');
                    exit();
                } else {
                    echo "Update query executed, but no rows were changed.";
                }
            } else {
                echo "Error updating record: " . implode(", ", $update_stmt->errorInfo());
            }
        } else {
            // No existing details, insert new record
            $insert_sql = "INSERT INTO user_details (user_id, name, address, phone, dob) VALUES (:user_id, :name, :address, :phone, :dob)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $insert_stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $insert_stmt->bindValue(':address', $address, PDO::PARAM_STR);
            $insert_stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
            $insert_stmt->bindValue(':dob', $dob, PDO::PARAM_STR);
            
            // Execute the query
            if ($insert_stmt->execute()) {
                echo "New record inserted successfully!";
                header('Location: home.php');
                exit();
            } else {
                echo "Error inserting record: " . implode(", ", $insert_stmt->errorInfo());
            }
        }
    } else {
        echo "All fields are required!";
    }
}
?>
