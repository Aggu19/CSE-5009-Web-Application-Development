<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/admin/template-admin.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
</head>
<body>
    <div class="sidebar">
        <div class="logo-Details">
            <a href="admin_home.php">
            <i class='bx bx-cube'></i></a>
            <span class="logo_name"; font-family="DM Serif Display";>Gallery Café</span>
            
        </div>
        
            <ul class="nav-links">
                <li>
               
                    <div class="icon-link">
                        <a href="#">
                            <i class='bx bx-sitemap'></i>
                            <span class="link_name">
                                Menu
                            </span>
                        </a><i class="bx bx-chevron-down arrow"></i>
                        
                    </div>
                    <ul class="sub-menu">
                        <li><a class="link_name" href=""> Manage</a></li>
                        <li><a href="add_menu.php">Add Menu</a></li>
                        <li><a href="edit_menu.php">Edit Menu</a></li>
                    </ul>
                
                </li>
                <li>
                    <div class="icon-link">
                        <a href="">
                            <i class='bx bxs-check-circle'></i>
                            <span class="link_name">
                                    Reservations
                            </span>
                        </a><i class="bx bx-chevron-down arrow"></i>
                    </div>
                    <ul class="sub-menu">
                        <li><a class="link_name" href=""> Reservation</a></li>
                        <li><a href="add_tables.php">Add Tables</a></li>
                        <li><a href="bookings.php">Manage</a></li>
                    </ul>
                    
                </li>
                <li>
                    <div class="icon-link">
                        <a href="pre-order-check.php">
                            <i class='bx bxs-heart-circle'></i>
                                <span class="link_name">
                                Check Pre-Order Meals
                                </span>
                            </a>
                          
                    </div>
                    
                </li>
                <li><a href="#">
                    <div class="icon-link">
                        <a href="#">
                        <i class='bx bxs-party'></i>
                            <span class="link_name">
                                Special Events
                            </span>
                        </a><i class="bx bx-chevron-down arrow"></i>
                        
                    </div>
                    <ul class="sub-menu">
                        <li><a class="link_name" href=""> Manage</a></li>
                        <li><a href="add_events.php">Add Events</a></li>
                        <li><a href="edit_event.php">Edit Events</a></li>
                    </ul>
            
                </li>
                <li><a href="manage_review.php">
                <i class='bx bx-edit'></i>
                        <span class="link_name">
                        Customer Reviews
                        </span>
                </a></li>
                <li><a href="add_staff.php">
                <i class='bx bxs-user'></i>
                    <span class="link_name">
                        Add Staff
                    </span>
                </a></li>
                <li><a href="../logout.php">
                <i class='bx bx-log-out'></i>
                    <span class="link_name">
                        Logout
                    </span>
                </a></li>

        </ul>    
    </div>
</body>
</html>