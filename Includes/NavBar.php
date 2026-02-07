<?php
// Includes/NavBar.php
require_once __DIR__ . '/Auth.php'; // Use __DIR__ for safety
$IsLoggedIn = AuthIsLoggedIn();
$UserRole = $_SESSION['user_role'] ?? 'Guest';

// Configuration Variables (Set these before including)
$HideNavLinks = $HideNavLinks ?? false;
$ShowBackButton = $ShowBackButton ?? false;
$BackLink = $BackLink ?? 'index.php';
?>
<nav class="Navbar">
    <div class="Logo">
        <a href="index.php">EPIX<span class="Highlight">GYM</span></a>
    </div>

    <?php if ($ShowBackButton): ?>
        <!-- Back Button Mode (e.g. for Tour/Login pages) -->
        <div class="NavActions">
            <a href="<?= htmlspecialchars($BackLink) ?>" class="Btn BtnOutline">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    <?php elseif (!$HideNavLinks): ?>
        <!-- Standard Navigation Mode -->
        <ul class="NavLinks">
            <li><a href="index.php#programs">Programs</a></li>
            <li><a href="index.php#memberships">Memberships</a></li>
            <li><a href="index.php#community">Community</a></li>
            <li><a href="Shop.php">Shop</a></li>
        </ul>

        <div class="NavActions">
            <?php if ($IsLoggedIn): ?>
                <?php if ($UserRole === 'Admin'): ?>
                    <a href="Pages/Admin/Dashboard.php" class="Btn BtnOutline">Dashboard</a>
                <?php elseif ($UserRole === 'Instructor'): ?>
                     <a href="Pages/Instructor/Dashboard.php" class="Btn BtnOutline">Dashboard</a>
                <?php else: ?>
                     <a href="Pages/Trainee/Dashboard.php" class="Btn BtnOutline">Profile</a>
                <?php endif; ?>
                <a href="Logout.php" class="Btn BtnOutline">Logout</a>
            <?php else: ?>
                <a href="Login.php" class="Btn BtnOutline">Login</a>
                <a href="Signup.php" class="Btn BtnPrimary">Join Now</a>
            <?php endif; ?>
        </div>
        
        <!-- Mobile Menu Icon -->
        <div class="MenuIcon">
            <div class="Bar"></div>
            <div class="Bar"></div>
            <div class="Bar"></div>
        </div>
    <?php endif; ?>
</nav>
