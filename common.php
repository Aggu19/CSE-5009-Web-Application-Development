<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/common-page.css">
    <title>GalleryCafe</title>
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
        
        <a href="admin/admin_login.php" class="card">
            <h2>Are you an Admin?</h2>
        </a>
        <a href="signup.php" class="card">
            <h2>Are you a Customer?</h2>
        </a>
        <a href="staff/staff_home.php" class="card">
            <h2>Are you a Staff?</h2>
        </a>
    </div>
    <div class="main_page">
        <a href="index.php">Main Page</a>
    </div>
</body>
</html>