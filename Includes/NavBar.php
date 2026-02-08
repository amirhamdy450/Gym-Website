<?php
// Includes/NavBar.php
require_once __DIR__ . '/Auth.php';
$IsLoggedIn = AuthIsLoggedIn();
$UserRole = $_SESSION['user_role'] ?? 'Guest';

// Configuration Variables
$HideNavLinks = $HideNavLinks ?? false;
$ShowBackButton = $ShowBackButton ?? false;
$BackLink = $BackLink ?? 'index.php';
$Prefix = $Prefix ?? ''; // Path prefix for subdirectories
?>
<nav class="Navbar">
    <div class="Logo">
        <a href="<?= $Prefix ?>index.php">EPIX<span class="Highlight">GYM</span></a>
    </div>

    <?php if ($ShowBackButton): ?>
        <!-- Back Button Mode -->
        <div class="NavActions">
            <a href="<?= $Prefix . htmlspecialchars($BackLink) ?>" class="Btn BtnOutline">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    <?php elseif (!$HideNavLinks): ?>
        <!-- Standard Navigation Mode -->
        <ul class="NavLinks">
            <li><a href="<?= $Prefix ?>index.php#programs">Programs</a></li>
            <li><a href="<?= $Prefix ?>index.php#memberships">Memberships</a></li>
            <li><a href="<?= $Prefix ?>index.php#community">Community</a></li>
            <li><a href="<?= $Prefix ?>Shop.php">Shop</a></li>
        </ul>

        <div class="NavActions">
            <?php if ($IsLoggedIn): ?>
                <?php if ($UserRole === 'Admin'): ?>
                    <a href="<?= $Prefix ?>Pages/Admin/Dashboard.php" class="Btn BtnOutline">Dashboard</a>
                <?php elseif ($UserRole === 'Instructor'): ?>
                    <a href="<?= $Prefix ?>Pages/Instructor/Dashboard.php" class="Btn BtnOutline">Dashboard</a>
                <?php else: ?>
                    <a href="<?= $Prefix ?>Pages/Trainee/Dashboard.php" class="Btn BtnOutline">Profile</a>
                <?php endif; ?>
                <a href="<?= $Prefix ?>Logout.php" class="Btn BtnOutline">Logout</a>
            <?php else: ?>
                <a href="<?= $Prefix ?>Login.php" class="Btn BtnOutline">Login</a>
                <a href="<?= $Prefix ?>Signup.php" class="Btn BtnPrimary">Join Now</a>
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