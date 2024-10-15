<?php
session_start(); // Ensure you start the session

if (isset($_POST["submit"])) {
    // Hardcoded admin credentials
    $adminUsername = "admin";
    $adminPassword = "admin123";

    // Get the input username and password
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if the input matches the hardcoded credentials
    if ($username === $adminUsername && $password === $adminPassword) {
        $_SESSION["username"] = $username;
        // Instead of redirecting immediately, use a JavaScript alert and then redirect
        echo "<script>
                alert('Login successful!');
                window.location.href = 'admin_home.php';
              </script>";
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var modal = document.getElementById('myModal');
                    modal.style.display = 'block';

                    var span = document.getElementsByClassName('close')[0];

                    // When the user clicks on <span> (x), close the modal
                    span.onclick = function() {
                        modal.style.display = 'none';
                    }

                    // When the user clicks anywhere outside of the modal, close it
                    window.onclick = function(event) {
                        if (event.target == modal) {
                            modal.style.display = 'none';
                        }
                    }
                });
            </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/common.css">
    <script type="text/javascript">
        function preventBack() {
            window.history.forward();
        };
        setTimeout("preventBack()", 0);
        window.onunload = function() {null;}
    </script>
</head>
<body>
    <div class="container">
        <div class="container-text">
            <h1 style="font-family: Delicious Handrawn;"> Gallery Café</h1>
            <p id="sub-text">Welcome to a taste of happiness.</p>
        </div>
       
        <div class="box form-box">
            <header>Admin Login</header>
            <form action="admin_login.php" method="post">
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
                    Customer login <a href="login.php">click here</a><br>
                    back to <a href="common-page.php">Back</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class='message'>
                <p>Invalid Admin Credentials</p>
            </div>
            <br>
        </div>
    </div>

</body>
</html>
