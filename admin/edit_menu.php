<?php
session_start(); // Start the session to use session variables
include('../connection.php'); // Adjust this path if necessary
$success = false; // Initialize the success variable

// Handle form submission
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $cuisine = $_POST['cuisine'];
    $type = $_POST['type'];
    $item = $_POST['item'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $vegan = isset($_POST['Vegan']) ? 1 : 0; // Set vegan to 1 if checkbox is checked, otherwise 0

    // Handle image upload
    if (isset($_FILES['img_dr']) && $_FILES['img_dr']['error'] == 0) {
        $img_name = $_FILES['img_dr']['name'];
        $img_tmp_name = $_FILES['img_dr']['tmp_name'];
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));

        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif']; // Allowed extensions

        if (in_array($img_ext, $allowed_ext)) {
            $img_new_name = uniqid('', true) . '.' . $img_ext; // Unique image name
            $img_destination = 'uploads/' . $img_new_name; // Set your upload folder path relative to this file

            if (move_uploaded_file($img_tmp_name, $img_destination)) {
                $img_path = $img_destination; // Save the relative path to the database
            } else {
                $_SESSION['alert'] = 'Error uploading the image!';
                header("Location: edit_menu.php");
                exit();
            }
        } else {
            $_SESSION['alert'] = 'Invalid image format!';
            header("Location: edit_menu.php");
            exit();
        }
    } else {
        // No new image uploaded, use the existing image path
        $img_path = $_POST['img_dr'];
    }

    // Update query with image path using PDO
    $sql2 = "UPDATE menu SET Cuisine = :cuisine, Type = :type, Item = :item, 
             Price = :price, Description = :description, Vegan = :vegan, 
             img_dr = :img_path WHERE id = :id";
    
    $stmt = $conn->prepare($sql2);
    $stmt->bindParam(':cuisine', $cuisine);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':item', $item);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':vegan', $vegan, PDO::PARAM_INT);
    $stmt->bindParam(':img_path', $img_path);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $_SESSION['alert'] = 'Menu item updated successfully!';
        header("Location: edit-menu.php"); // Redirect to avoid form resubmission
        exit();
    } else {
        $_SESSION['alert'] = 'Error updating menu item!';
        header("Location: edit-menu.php"); // Redirect to display the error message
        exit();
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
    <link rel="stylesheet" href="../css/admin/edit-menu.css">
    <script src="../js/common.js" defer></script>
    <title>Admin</title>
</head>
<body>
    <?php include('nav_admin.php'); ?>
    
    <section class="home-section">
        <div class="home-content">
            <i class="bx bx-menu"></i>
            <span class="text">Edit Menu</span>
        </div>
        <div class="next">
            <div class="cards">
            <?php
                // Select menu items using PDO
                $sql = "SELECT * FROM menu";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($results) > 0) {
                    foreach ($results as $fetch) {
                        echo '<div class="card">';
                        echo '<div class="card-content">';
                        // Display image if available
                        if (!empty($fetch['img_dr'])) {
                            echo '<img src="' . htmlspecialchars($fetch['img_dr']) . '" alt="' . htmlspecialchars($fetch['Item']) . '" style="width: 100%; height: auto;">';
                        }
                        echo '<h3>' . htmlspecialchars($fetch['Item']) . '</h3>';
                        echo '<p>Cuisine: ' . htmlspecialchars($fetch['Cuisine']) . '</p>';
                        echo '<p>Price: Rs ' . htmlspecialchars($fetch['Price']) . '</p>';
                        echo '<p>' . htmlspecialchars($fetch['Description']) . '</p>';
                        echo '</div>'; // Close card-content
                        echo '<div class="card-footer">';
                        echo '<input class="modal-btn" data-id="' . $fetch['id'] . '" data-cuisine="' . $fetch['Cuisine'] . '" data-type="' . $fetch['Type'] . '" data-item="' . $fetch['Item'] . '" data-price="' . $fetch['Price'] . '" data-img_dr="' . $fetch['img_dr'] . '" data-description="' . $fetch['Description'] . '" data-vegan="' . $fetch['Vegan'] . '" type="submit" value="Edit">';
                        echo '<input type="delete" class="btn" value="Delete" onclick="location.href=\'delete.php?id=' . $fetch['id'] . '\'">';
                        echo '</div>'; // Close card-footer
                        echo '</div>'; // Close card
                    }
                } else {
                    echo '<p>No menu items found.</p>';
                }
            ?>
            </div>
        </div>

        <!-- Edit pop-up part -->
        <div class="next-content" id="edit-popup">
            <div class="box form-box">
                <form method="POST" enctype="multipart/form-data">
                    <div class="button_cancel">
                        <button type="button" id="close-edit-popup" class="cancel_icon"><i class='bx bx-x'></i></button>
                    </div>
                    
                    <input type="hidden" id="note_id" name="id">
                    
                    <div class="field input">
                        <label for="edit_cuisine">Cuisine:</label>
                        <input type="text" id="edit_cuisine" name="cuisine" placeholder="Your cuisine" autocomplete="off">
                    </div>
                    
                    <div class="field input">
                        <label for="edit_type">Type:</label>
                        <input type="text" id="edit_type" name="type" placeholder="Your type" autocomplete="off">
                    </div>
                    
                    <div class="field input">
                        <label for="edit_item">Item:</label>
                        <input type="text" id="edit_item" name="item" placeholder="Your item" autocomplete="off">
                    </div>
                    
                    <div class="field input">
                        <label for="edit_price">Price:</label>
                        <input type="number" id="edit_price" name="price" placeholder="Your price" autocomplete="off">
                    </div>
                    
                    <div class="field input">
                        <label for="edit_img_dr">Image:</label>
                        <input type="file" id="edit_img_dr" name="img_dr">
                    </div>
                    
                    <div class="field input">
                        <label for="edit_description">Description:</label>
                        <textarea id="edit_description" name="description" rows="4" autocomplete="off" placeholder="Your description..."></textarea>
                    </div>
                    
                    <div class="vegan-checkbox">
                        <input type="checkbox" id="edit_Vegan" name="Vegan">
                        <label for="edit_Vegan">Vegan</label>
                    </div><br>
                    <div class="field-btn">
                        <input type="submit" class="btn1" name="update" value="Update">
                        <input type="reset" class="btn1" name="cancel" value="Cancel"> 
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- JavaScript for edit pop-up -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editButtons = document.querySelectorAll('.modal-btn');
            const editPopup = document.getElementById('edit-popup');
            const closeEditPopupButton = document.getElementById('close-edit-popup');
            const noteIdInput = document.getElementById('note_id');
            const editCuisineInput = document.getElementById('edit_cuisine');
            const editTypeInput = document.getElementById('edit_type');
            const editItemInput = document.getElementById('edit_item');
            const editPriceInput = document.getElementById('edit_price');
            const editDescriptionInput = document.getElementById('edit_description');
            const editVeganInput = document.getElementById('edit_Vegan');

            // Edit button event listener
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    noteIdInput.value = this.dataset.id;
                    editCuisineInput.value = this.dataset.cuisine;
                    editTypeInput.value = this.dataset.type;
                    editItemInput.value = this.dataset.item;
                    editPriceInput.value = this.dataset.price;
                    editDescriptionInput.value = this.dataset.description;
                    editVeganInput.checked = this.dataset.vegan == 1;

                    // Load existing image
                    const imgPath = this.dataset.img_dr;
                    if (imgPath) {
                        const imgElement = document.createElement('img');
                        imgElement.src = imgPath; // Use the dataset for the image source
                        imgElement.alt = 'Image';
                        imgElement.style.width = '100%'; // Adjust size as necessary
                        editPopup.appendChild(imgElement);
                    }

                    editPopup.style.display = 'block'; // Show the edit popup
                });
            });

            // Close popup button event listener
            closeEditPopupButton.addEventListener('click', function() {
                editPopup.style.display = 'none'; // Hide the edit popup
                // Clear the popup inputs and image for the next use
                noteIdInput.value = '';
                editCuisineInput.value = '';
                editTypeInput.value = '';
                editItemInput.value = '';
                editPriceInput.value = '';
                editDescriptionInput.value = '';
                editVeganInput.checked = false;

                // Remove existing image from the popup
                const existingImage = editPopup.querySelector('img');
                if (existingImage) {
                    editPopup.removeChild(existingImage);
                }
            });
        });
    </script>
</body>
</html>
