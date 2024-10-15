<?php
session_start();
include('connection.php'); // Ensure this file initializes the PDO connection correctly

if (isset($_POST["submit"])) {
    // Get the input values
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Prepare the SQL statement to check if the username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    
    try {
        // Execute the statement
        $stmt->execute();
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existingUser) {
            // Hash the password before storing
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'customer')");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);

            if ($stmt->execute()) {
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
            } else {
                echo "<div class='message'>
                        <p>Error: " . $stmt->errorInfo()[2] . "</p>
                      </div> <br>";
            }
        } else {
            // If username or email exists, show an alert and redirect
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        alert('User already exists');
                        window.location.href = 'login.php';
                    });
                  </script>";
        }
    } catch (PDOException $e) {
        // Handle any error
        echo "<div class='message'>
                <p>Error: " . $e->getMessage() . "</p>
              </div> <br>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-up</title>
    <link rel="stylesheet" href="css/common.css">
</head>
<body>
    <div class="container">
        <div class="container-text">
            <h1 style="font-family: Delicious Handrawn;">Gallery Café</h1>
            <p id="sub-text">Escape the ordinary.</p>
        </div>
        <div class="box form-box">
            <header>Sign-up</header>
            <form action="signup.php" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label for="email">E-mail</label>
                    <input type="email" name="email" id="email" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Sign-Up" required>
                </div>
                <div class="link" style="color: aliceblue;">
                    Already a member? <a href="login.php">Log-in Now</a>
                </div>
                <div class="link" style="color: aliceblue;">
                    Back to <a href="common-page.php">Home</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class='message'>
                <p>Registration successful!</p>
            </div>
            <br>
            <a href='login.php'><button class='btn-log'>Login Now</button></a>
        </div>
    </div>
</body>
</html>
