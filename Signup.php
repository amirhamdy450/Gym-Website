<?php
// Signup.php (Root Entry Point)
require_once 'Includes/DB.php';
require_once 'Includes/Auth.php';
require_once 'Includes/Memberships.php';

// Redirect if already logged in
if (AuthIsLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Fetch Membership Plans (Logic at top)
$Plans = GetMemberships($pdo);

// Pre-process Plans Data for View (Not strictly needed if we iterate directly, 
// but we keep consistent with previous refactor. 
// Actually, strict iteration in View is cleaner for complex HTML structures sometimes.)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Epix Gym | Create Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/signup.css">
</head>
<body class="SignupBody">

    <?php 
    $HideNavLinks = true;
    include 'Includes/NavBar.php'; 
    ?>

    <div class="WizardContainer">
        
        <!-- ====================
             STEP 1: IDENTITY
             ==================== -->
        <div id="Step1" class="StepGroup Active">
            <div class="StepIndicator">
                <span class="StepPill">Step 1 of 3</span>
            </div>
            <h2>Personal Identity</h2>
            <p class="SubText">Let's start with your basic identification</p>

            <form id="FormStep1">
                <div class="FormRow">
                    <div class="Col FormGroup">
                        <label for="FirstName">First Name</label>
                        <input type="text" id="FirstName" class="InputBox" placeholder="John" required>
                    </div>
                    <div class="Col FormGroup">
                        <label for="LastName">Last Name</label>
                        <input type="text" id="LastName" class="InputBox" placeholder="Doe" required>
                    </div>
                </div>

                <div class="FormGroup">
                    <label for="Email">Email Address</label>
                    <input type="email" id="Email" class="InputBox" placeholder="john@example.com" required>
                </div>

                <div class="FormRow">
                    <div class="Col FormGroup">
                        <label for="DateOfBirth">Date of Birth</label>
                        <input type="date" id="DateOfBirth" class="InputBox" required>
                    </div>
                    <div class="Col FormGroup">
                        <label>Gender</label>
                        <div class="GenderGrid">
                            <label class="GenderOption" id="LabelMale">
                                Male
                                <input type="radio" name="Gender" value="Male">
                            </label>
                            <label class="GenderOption" id="LabelFemale">
                                Female
                                <input type="radio" name="Gender" value="Female">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="FormRow">
                    <div class="Col FormGroup">
                        <label for="Password">Password</label>
                        <input type="password" id="Password" class="InputBox" placeholder="••••••••" required minlength="8">
                    </div>
                    <div class="Col FormGroup">
                        <label for="ConfirmPassword">Confirm</label>
                        <input type="password" id="ConfirmPassword" class="InputBox" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="button" id="BtnStep1Next" class="BtnPrimary">NEXT: PHYSICAL PROFILE</button>

                <div class="LoginLink">
                    Already have an account? <a href="Login.php">Login</a>
                </div>
            </form>
        </div>

        <!-- ====================
             STEP 2: PHYSICAL
             ==================== -->
        <div id="Step2" class="StepGroup">
            <div class="StepIndicator">
                <span class="StepPill">Step 2 of 3</span>
            </div>
            <h2>Physical Profile</h2>
            <p class="SubText">Help us tailor your fitness experience</p>

            <form id="FormStep2">
                <div class="FormRow">
                    <div class="Col FormGroup">
                        <label for="Height">Height (cm)</label>
                        <input type="number" id="Height" class="InputBox" placeholder="180" min="100" max="250">
                    </div>
                    <div class="Col FormGroup">
                        <label for="Weight">Current Weight (kg)</label>
                        <input type="number" id="Weight" class="InputBox" placeholder="75" min="30" max="300">
                    </div>
                </div>

                <div class="FormGroup">
                    <label>Fitness Goals</label>
                    <div class="GoalGrid">
                        <div class="GoalOption" data-goal="Lose Weight" id="GoalLoseWeight">Lose Weight</div>
                        <div class="GoalOption" data-goal="Build Muscle" id="GoalBuildMuscle">Build Muscle</div>
                        <div class="GoalOption" data-goal="Endurance" id="GoalEndurance">Endurance</div>
                        <div class="GoalOption" data-goal="Flexibility" id="GoalFlexibility">Flexibility</div>
                        <div class="GoalOption" data-goal="Strength" id="GoalStrength">Strength</div>
                        <div class="GoalOption" data-goal="Health" id="GoalHealth">Health</div>
                    </div>
                </div>

                <button type="button" id="BtnStep2Next" class="BtnPrimary">NEXT: MEMBERSHIP</button>
                <button type="button" id="BtnStep2Back" class="BtnSecondary">Back</button>

            </form>
        </div>

        <!-- ====================
             STEP 3: MEMBERSHIP
             ==================== -->
        <div id="Step3" class="StepGroup">
            <div class="StepIndicator">
                <span class="StepPill">Step 3 of 3</span>
            </div>
            <h2>Choose Plan</h2>
            <p class="SubText">Select the plan that fits your journey</p>

            <div class="PlanListGrid">
                <!-- Generated Plans from DB -->
                <?php foreach ($Plans as $Plan): 
                    // Parse Features
                    $Features = json_decode($Plan['FeaturesJson'] ?? '[]', true);
                    $CoreFeatures = $Features['Core'] ?? [];
                    $PremiumFeatures = $Features['Premium'] ?? [];
                    $LockedFeatures = $Features['Locked'] ?? [];
                    
                    // Theme
                    $ThemeClass = 'Plan-Bronze';
                    if (stripos($Plan['Name'], 'Silver') !== false) $ThemeClass = 'Plan-Silver';
                    if (stripos($Plan['Name'], 'Gold') !== false || stripos($Plan['Name'], 'Platinum') !== false) $ThemeClass = 'Plan-Platinum';
                    
                    // Popular
                    $IsPopular = (stripos($Plan['Name'], 'Silver') !== false);
                ?>
                    <div class="SelectablePlan <?= $ThemeClass ?>" data-id="<?= $Plan['id'] ?>">
                         <?php if($IsPopular): ?><div class="Badge">TRENDING</div><?php endif; ?>
                        
                        <div class="PlanHeader">
                            <span class="PlanName"><?php echo strtoupper($Plan['Description']); ?></span>
                            <h3 class="PlanNameTitle"><?php echo $Plan['Name']; ?></h3>
                            <div class="PlanPrice">$<?php echo intval($Plan['Price']); ?><span class="Period">/month</span></div>
                        </div>

                        <div class="PlanFeaturesContainer">
                            <h5 class="PlanFeaturesTitle">CORE FEATURES</h5>
                            <ul class="PlanFeatures">
                                <?php foreach ($CoreFeatures as $Feature): ?>
                                <li class="FeatureEnabled"><?php echo htmlspecialchars($Feature); ?></li>
                                <?php endforeach; ?>
                            </ul>

                            <?php if (!empty($PremiumFeatures) || !empty($LockedFeatures)): ?>
                            <h5 class="PlanFeaturesTitle">PREMIUM PERKS</h5>
                            <ul class="PlanFeatures">
                                <?php foreach ($PremiumFeatures as $Feature): ?>
                                <li class="FeatureEnabled"><?php echo htmlspecialchars($Feature); ?></li>
                                <?php endforeach; ?>
                                
                                <?php foreach ($LockedFeatures as $Feature): ?>
                                <li class="FeatureLocked"><?php echo htmlspecialchars($Feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </div>

                        <div class="FakeButton">SELECT PLAN</div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="WizardActions">
                 <button type="button" id="BtnStep3Back" class="BtnSecondary">Back</button>
                 <button type="button" id="FinalSubmitBtn" class="BtnPrimary BtnDisabled" disabled>COMPLETE SIGNUP</button>
            </div>
        </div>

    </div>

    <script src="js/signup.js"></script>
</body>
</html>
