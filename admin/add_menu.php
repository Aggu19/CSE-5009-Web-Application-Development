<?php
session_start(); 
include('../connection.php'); 

// Check if database connection is working
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

error_reporting(E_ALL);
ini_set('display_errors', 1); // Enable error reporting

$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Form submitted successfully!"; // For testing form submission
    
    // Collect form data
    $cuisine = $_POST["cuisine"];
    $type = $_POST["type"];
    $item = $_POST["item"];
    $price = $_POST["price"];
    $description = $_POST["description"];
    $vegan = isset($_POST["Vegan"]) ? 1 : 0;

    // Handle the image upload
    $img_dr = $_FILES["img_dr"];
    $img_dr_name = $img_dr["name"];
    $img_dr_tmp_name = $img_dr["tmp_name"];
    $img_dr_size = $img_dr["size"];
    $img_dr_error = $img_dr["error"]; // Capture any upload error

    // Check if a file was uploaded and handle errors
    if ($img_dr_error === UPLOAD_ERR_OK) {
        $img_dr_type = mime_content_type($img_dr_tmp_name); // Get the mime type for better validation

        // Check image properties
        echo "Image type: " . $img_dr_type . "<br>";
        echo "Image size: " . $img_dr_size . " bytes<br>";

        // Validate the image type and size
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if ($img_dr_size > 0 && in_array($img_dr_type, $allowed_types)) {
            $upload_dir = __DIR__ . "/uploads/"; // Ensure correct directory path
            $img_dr_path = $upload_dir . basename($img_dr_name);
            
            // Create the uploads directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Move the uploaded file
            if (move_uploaded_file($img_dr_tmp_name, $img_dr_path)) {
                echo "File uploaded successfully!"; // For testing file upload
                
                // Insert the data into the database using PDO
                $stmt = $conn->prepare("INSERT INTO menu (Item, Cuisine, Type, Price, Vegan, Description, img_dr) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if (!$stmt) {
                    echo "Prepare failed: (" . $conn->errorInfo()[2] . ")";
                }

                // Bind the parameters using bindValue for PDO
                $stmt->bindValue(1, $item);
                $stmt->bindValue(2, $cuisine);
                $stmt->bindValue(3, $type);
                $stmt->bindValue(4, $price);
                $stmt->bindValue(5, $vegan);
                $stmt->bindValue(6, $description);
                $stmt->bindValue(7, $img_dr_name);

                // Execute the statement
                if ($stmt->execute()) {
                    echo "Menu item inserted successfully!"; // For testing database insertion
                    $_SESSION['alert'] = "Menu item added successfully!";
                    header("Location: add_menu.php");
                    exit();
                } else {
                    echo "Error adding menu item: " . implode(", ", $stmt->errorInfo());
                }
            } else {
                echo "Failed to upload image.";
            }
        } else {
            echo "Invalid image file. Only JPEG, PNG, and JPG formats are allowed.";
        }
    } else {
        // Handle different upload errors
        switch ($img_dr_error) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                echo "The uploaded file exceeds the maximum allowed size.";
                break;
            case UPLOAD_ERR_NO_FILE:
                echo "No file was uploaded.";
                break;
            case UPLOAD_ERR_PARTIAL:
                echo "The uploaded file was only partially uploaded.";
                break;
            default:
                echo "An unknown error occurred during file upload.";
                break;
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
    <link rel="stylesheet" href="../css/admin/add-menu.css">
    <script src="../js/common.js" defer></script>
    <title>Admin - Add Menu</title>
</head>
<body>
    <?php include('nav_admin.php'); ?>
    
    <section class="home-section">
        <div class="home-content">
            <i class="bx bx-menu"></i>
            <span class="text">Add Menu</span>
        </div>
        <div class="next">
            <form action="add_menu.php" method="post" enctype="multipart/form-data" class="form-menu">
                <label for="cuisine">Cuisine:</label>
                <select id="cuisine" name="cuisine" required>
                    <option value="">--Select Cuisine--</option>
                    <option value="Sri Lankan">Sri Lankan</option>
                    <option value="Chinese">Chinese</option>
                    <option value="Italian">Italian</option>
                    <option value="Indian">Indian</option>
                </select><br><br>

                <label for="type">Type:</label>
                <input type="text" id="type" name="type" required><br><br>

                <label for="item">Item:</label>
                <input type="text" id="item" name="item" required><br><br>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" required><br><br>

                <label for="img_dr">Image:</label>
                <input type="file" id="img_dr" name="img_dr" required><br><br>

                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea><br><br>

                <div class="vegan-checkbox">
                    <input type="checkbox" id="Vegan" name="Vegan">
                    <label for="Vegan">Vegan</label>
                </div><br>

                <input type="submit" value="Add Menu Item">
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
