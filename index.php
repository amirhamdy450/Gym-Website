<?php
// index.php
require_once 'Includes/DB.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Epix Gym | Evolve Beyond Limits</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    
    <!-- Navigation -->
    <!-- Navigation -->
    <?php include 'Includes/NavBar.php'; ?>

    <!-- Hero Section -->
    <header class="Hero">
        <div class="HeroContent">
            <span class="Tagline">FUTURE OF FITNESS</span>
            <h1>EVOLVE<br><span class="OutlineText">BEYOND</span><br>LIMITS</h1>
            <p>Experience elite coaching and state-of-the-art facilities designed to push you further. No excuses, just results.</p>
            <div class="HeroButtons">
                <a href="Signup.php" class="Btn BtnPrimary">START YOUR JOURNEY</a>
                <a href="TourBooking.php" class="Btn BtnOutline">BOOK A TOUR</a>
            </div>
        </div>
        <div class="HeroImage">
            <img src="imgs/hero-runner.png" alt="Evolve Beyond Limits" class="HeroImg">
        </div>
    </header>

    <!-- Memberships Section -->
    <section id="memberships" class="SectionDark">
        <div class="SectionHeader">
            <h2>MEMBERSHIP PLANS</h2>
            <p>Choose the tier that matches your ambition. All plans include full app access.</p>
        </div>
        
        <div class="PlansGrid">
            <?php
            require_once 'Includes/Memberships.php';
            $Plans = GetMemberships($pdo);

            foreach ($Plans as $Plan): 
                $Features = json_decode($Plan['FeaturesJson'] ?? '[]', true);
                $CoreFeatures = $Features['Core'] ?? [];
                $PremiumFeatures = $Features['Premium'] ?? [];
                $LockedFeatures = $Features['Locked'] ?? [];
                
                $IsPopular = $Plan['Name'] === 'SILVER';
                $CardClass = $IsPopular ? 'PlanCard Popular' : 'PlanCard';
                $BtnClass = $IsPopular ? 'Btn BtnWhite FullWidth' : ($Plan['Name'] === 'PLATINUM' ? 'Btn BtnPrimary FullWidth' : 'Btn BtnOutline FullWidth');
                $FinalBtnClass = $BtnClass . " PlanButton";
            ?>
            <div class="<?php echo $CardClass; ?>">
                <?php if($IsPopular): ?><div class="Badge">TRENDING</div><?php endif; ?>
                <div class="PlanHeader">
                    <span class="PlanName"><?php echo strtoupper($Plan['Description']); ?></span>
                    <h3 class="PlanNameTitle"><?php echo $Plan['Name']; ?></h3>
                    <div class="PlanPrice">$<?php echo intval($Plan['Price']); ?><span class="Period">/month</span></div>
                </div>
                
                <div class="PlanFeaturesContainer">
                    <h5 class="PlanFeaturesTitle">CORE FEATURES</h5>
                    <ul>
                        <?php foreach ($CoreFeatures as $Feature): ?>
                        <li class="FeatureEnabled"><?php echo htmlspecialchars($Feature); ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if (!empty($PremiumFeatures) || !empty($LockedFeatures)): ?>
                    <h5 class="PlanFeaturesTitle">PREMIUM PERKS</h5>
                    <ul>
                        <?php foreach ($PremiumFeatures as $Feature): ?>
                        <li class="FeatureEnabled"><?php echo htmlspecialchars($Feature); ?></li>
                        <?php endforeach; ?>
                        
                        <?php foreach ($LockedFeatures as $Feature): ?>
                        <li class="FeatureLocked"><?php echo htmlspecialchars($Feature); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>

                <button class="<?php echo $FinalBtnClass; ?>">CHOOSE <?php echo strtoupper($Plan['Name']); ?></button>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Community Section -->
    <section id="community" class="SectionDark">
        <div class="CommunityHeader">
            <div class="CommunityLine"></div>
            <span class="CommunityTitle">COMMUNITY PROOF</span>
        </div>
        
        <div class="CommunityContent">
            <div class="CommunityText">
                <h2>EXPERIENCE<br><span class="Highlight">THE ENERGY</span></h2>
                
                <div class="Stats">
                    <div class="StatItem">
                        <span>10k+</span>
                        <span>ACTIVE MEMBERS</span>
                    </div>
                    <div class="StatItem">
                        <span class="StatRating">4.9 ★</span>
                        <span>*****</span>
                    </div>
                </div>

                <div class="FeatureCard FeatureCardMain">
                    <div class="FeatureOverlay">
                        <h4>IRON PARADISE</h4>
                        <p>Premium equipment for peak performance.</p>
                    </div>
                    <img src="imgs/gym-iron-paradise.png" alt="Gym Iron Paradise" class="FeatureImage">
                </div>
            </div>

            <div class="CommunityImages">
                <div class="ImgCol Padded">
                    <div class="FeatureCard FeatureTall">
                         <div class="FeatureOverlay">
                            <h4>HIIT SESSIONS</h4>
                            <p>High intensity, high reward atmosphere.</p>
                        </div>
                        <img src="imgs/gym-hiit.png" alt="HIIT Sessions" class="FeatureImage">
                    </div>
                </div>
                <div class="ImgCol">
                     <div class="FeatureCard FeatureMid">
                         <div class="Overlay">
                            <h4>EXPERT COACHING</h4>
                            <p>Unlock your true athletic potential.</p>
                        </div>
                        <img src="imgs/expert-coach.png" alt="Expert Coach" class="FeatureImage">
                    </div>
                     <div class="FeatureCard FeatureSmall">
                         <div class="Overlay">
                            <h4>RECOVERY ZONE</h4>
                            <p>Dedicated restoration.</p>
                        </div>
                        <img src="imgs/gym-recovery.png" alt="Recovery Zone" class="FeatureImage">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <!-- Footer -->
    <?php include 'Includes/Footer.php'; ?>

</body>
</html>
