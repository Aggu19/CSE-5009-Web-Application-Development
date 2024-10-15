<?php
session_start();
include('../connection.php');
$success = false;

// Handle the event submission
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $event_name = $_POST['e_name'];
    $event_description = $_POST['e_description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Use a prepared statement with PDO
    $sql = "UPDATE `events` SET `e_name` = :e_name, `e_description` = :e_description, `start_date` = :start_date, `end_date` = :end_date WHERE `id` = :id";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':e_name', $event_name);
    $stmt->bindParam(':e_description', $event_description);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $_SESSION['alert'] = 'Event updated successfully!';
    } else {
        $_SESSION['alert'] = 'Error updating event!';
    }

    // Redirect to avoid form resubmission
    header("Location: edit_event.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/admin/template-admin.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../admin/add-menu.css">
    <link rel="stylesheet" href="../css/admin/add-specialEvents.css">
    <script src="../js/common.js" defer></script>
    <title>Admin - Add Event</title>
</head>
<body>
    <?php include('nav_admin.php'); ?>

    <section class="home-section">
        <div class="home-content">
            <i class="bx bx-menu"></i>
            <span class="text">Edit Events</span>
        </div>
        <div class="next">
            <div class="cards">
            <!-- Selecting event items -->
                <?php
                    // Use PDO to fetch events
                    $sql = "SELECT * FROM `events`";
                    $stmt = $conn->query($sql);
                    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($events) > 0) {
                        foreach ($events as $event) {
                            echo '<div class="card">';
                            echo '<div class="card-content">';
                            echo '<h3>' . htmlspecialchars($event['e_name']) . '</h3>';
                            echo '<p>' . htmlspecialchars($event['e_description']) . '</p>';
                            echo '<p>From: ' . htmlspecialchars($event['start_date']) . '</p>';
                            echo '<p>To: ' . htmlspecialchars($event['end_date']) . '</p>';
                            echo '</div>';
                            echo '<div class="card-footer">';
                            echo '<input class="modal-btn" data-id="' . $event['id'] . '" data-e_name="' . $event['e_name'] . '" data-e_description="' . $event['e_description'] . '" data-start_date="' . $event['start_date'] . '" data-end_date="' . $event['end_date'] . '" type="submit" value="Edit" name="edit">';
                            echo '<input type="delete" class="btn" value="Delete" onclick="location.href=\'e_delete.php?id=' . $event['id'] . '\'">';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No events found</p>';
                    }
                ?>
            </div>
        </div>

        <!-- Edit pop-up modal -->
         <div class="next-content" id="edit-popup">
            <div class="box form-Event">
                <form method="POST">
                    <div class="button_cancel">
                        <button type="button" id="close-edit-popup" class="cancel_icon"><i class='bx bx-x'></i></button>
                        <input type="hidden" id="event_id" name="id">
                    </div>
                    <div class="field input">
                        <label for="edit_name">Event Name:</label>
                        <input type="text" id="edit_name" name="e_name" placeholder="Enter event name..." autocomplete="off">
                    </div>
                    <div class="field input">
                        <label for="edit_description">Event Description:</label>
                        <textarea type="text" id="edit_description" name="e_description" placeholder="Enter event description..." autocomplete="off"></textarea>
                    </div>
                    <div class="field input">
                        <label for="edit_start_date">Start Date:</label>
                        <input type="date" id="edit_start_date" name="start_date" autocomplete="off">
                    </div>
                    <div class="field input">
                        <label for="edit_end_date">End Date:</label>
                        <input type="date" id="edit_end_date" name="end_date" autocomplete="off">
                    </div>
                    <div class="field-btn">
                        <input type="submit" class="btn1" name="update" value="Update">
                        <input type="reset" class="btn1" name="cancel" value="Cancel">
                    </div>
                </form>
            </div>
         </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const editBtns = document.querySelectorAll('.modal-btn');
            const editPopup = document.getElementById('edit-popup');
            const closeEditPopupButton = document.getElementById('close-edit-popup');
            const editName = document.getElementById('edit_name');
            const editDescription = document.getElementById('edit_description');
            const editStartDate = document.getElementById('edit_start_date');
            const editEndDate = document.getElementById('edit_end_date');
            const eventId = document.getElementById('event_id');

            editBtns.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const e_name = this.getAttribute('data-e_name');
                    const e_description = this.getAttribute('data-e_description');
                    const start_date = this.getAttribute('data-start_date');
                    const end_date = this.getAttribute('data-end_date');

                    editName.value = e_name;
                    editDescription.value = e_description;
                    editStartDate.value = start_date;
                    editEndDate.value = end_date;
                    eventId.value = id;

                    editPopup.classList.add('active');
                });
            });

            closeEditPopupButton.addEventListener('click', function() {
                editPopup.classList.remove('active');
            });
        });
    </script>
</body>
</html>
