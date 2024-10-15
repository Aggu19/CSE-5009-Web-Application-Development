<?php
session_start();
include('connection.php'); // Ensure this file initializes the PDO connection correctly

if (isset($_POST["submit"])) {
    // Get the input values
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Prepare the SQL statement to query the database for the user
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);

    try {
        // Execute the statement
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the user exists and verify the password
        if ($row && password_verify($password, $row["password"])) {
            $_SESSION["username"] = $username;
            $_SESSION["user_id"] = $row["id"]; // Store user_id in session

            header("Location: home.php");
            exit(); // Important to prevent further code execution after redirect
        } else {
            // Display an error message if authentication fails
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var modal = document.getElementById('myModal');
                        modal.style.display = 'block';

                        var span = document.getElementsByClassName('close')[0];

                        span.onclick = function() {
                            modal.style.display = 'none';
                        }

                        window.onclick = function(event) {
                            if (event.target == modal) {
                                modal.style.display = 'none';
                            }
                        }
                    });
                </script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/common.css">
</head>
<body>
    <div class="container">
        <div class="image">
            <!-- <img src="asset/logo.png" alt="image"> -->
        </div> 
        <div class="container-text">
            <h1 style="font-family: Delicious Handrawn;">Gallery Café</h1>
            <p id="sub-text">Welcome to a taste of happiness.</p>
        </div>
       
        <div class="box form-box">
            <header>Login</header>
            <form action="login.php" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Login" required>
                </div>
                <div class="link" style="color: aliceblue;">
                    Don't have an account? <a href="signup.php">Sign-up Now</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class='message'>
                <p>User Does Not Exist</p>
            </div>
            <br>
            <a href='signup.php'><button class='btn-log'>Sign-Up</button></a>
        </div>
    </div>

</body>
</html>
