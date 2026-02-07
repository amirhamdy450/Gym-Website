<?php
// TourSuccess.php
require_once 'Includes/DB.php';

// Fetch Active Offer
$offer = null;
try {
    $stmt = $pdo->query("SELECT * FROM specialoffers WHERE IsActive = 1 LIMIT 1");
    $offer = $stmt->fetch();
} catch (PDOException $e) { }

// Get Params
$dateRaw = $_GET['date'] ?? date('Y-m-d');
$timeStr = $_GET['time'] ?? 'soon';
$location = $_GET['location'] ?? 'EPIX GYM';

// Format Date
$dateObj = new DateTime($dateRaw);
$dateDisplay = $dateObj->format('F jS');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Scheduled | EPIX GYM</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/tour.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="TourBody">

    <!-- Navigation -->
    <?php 
    $ShowBackButton = false; // Disable back button per user request
    $HideNavLinks = true;    
    include 'Includes/NavBar.php'; 
    ?>

    <div class="TourPageWrapper" style="justify-content: center;">
        
        <div class="SuccessCard">
            <div class="CheckIcon">
                <i class="fa-solid fa-check" style="color: white; font-size: 2rem;"></i>
            </div>
            
            <h1 class="SuccessTitle">
                YOUR TOUR IS <br> <span class="Highlight">SCHEDULED!</span>
            </h1>
            
            <p class="SuccessText">
                We look forward to seeing you at <strong><?= htmlspecialchars($location) ?></strong><br>
                on <strong><?= $dateDisplay ?> at <?= htmlspecialchars($timeStr) ?></strong>.
            </p>

            <!-- What to Expect Grid -->
            <div class="FeatureGrid">
                <div class="FeatureGridItem">
                    <i class="fa-solid fa-id-card"></i>
                    <div>Meet Guide</div>
                </div>
                <div class="FeatureGridItem">
                    <i class="fa-solid fa-dumbbell"></i>
                    <div>Check Gear</div>
                </div>
                <div class="FeatureGridItem">
                    <i class="fa-solid fa-comments"></i>
                    <div>Q&A Session</div>
                </div>
            </div>

            <!-- Dynamic Offer -->
            <?php if ($offer): ?>
            <div class="OfferCard">
                <i class="fa-solid fa-tag" style="color: var(--primary); margin-right: 0.5rem;"></i>
                <span class="Tag">Limited Time Offer</span>
                
                <h3>
                    <?= htmlspecialchars($offer['Description']) ?>
                </h3>
                
                <p>
                    Exclusive offer valid only for tour attendees who sign up on the same day.
                </p>
                
                <a href="Pricing.php" class="OfferArrow">
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <?php endif; ?>

            <div class="ActionButtons">
                <a href="Pricing.php" class="BtnSubmit" style="text-decoration: none; flex: 1;">Explore Memberships</a>
                <a href="index.php" class="BtnSubmit BtnHome">Home</a>
            </div>

        </div>
    </div>

</body>
</html>
