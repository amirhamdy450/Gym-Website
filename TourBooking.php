<?php
// TourBooking.php
require_once 'Includes/DB.php';

// Fetch Locations for Dropdown
$locations = [];
try {
    $stmt = $pdo->query("SELECT id, Name FROM locations");
    $locations = $stmt->fetchAll();
} catch (PDOException $e) {
    // Handle error quietly or log
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Visit | EPIX GYM</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Global -->
    <link rel="stylesheet" href="css/tour.css">  <!-- Page Specific -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="TourBody">

    <!-- Navigation -->
    <!-- Navigation -->
    <?php 
    $ShowBackButton = false; // Disabled in Nav, moving to content
    $HideNavLinks = true;    // Still hide main links
    include 'Includes/NavBar.php'; 
    ?>

    <div class="TourPageWrapper">
        <!-- Left Text Content -->
        <div class="TourContent">
            <h1>SEE THE <br><span class="Highlight">ENERGY</span> FOR <br>YOURSELF</h1>
            <p>Experience premium equipment, expert coaching, and a community that pushes you further. Book your free VIP tour today.</p>
            
            <div class="BenefitsList">
                <div class="BenefitItem">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>No commitment required</span>
                </div>
                <div class="BenefitItem">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Personalized walk-through</span>
                </div>
            </div>

            <!-- Moved Back Button -->
            <a href="index.php" class="BtnBackContent">
                <i class="fa-solid fa-arrow-left"></i> Back to Home
            </a>
        </div>

        <!-- Right Booking Form -->
        <div class="TourFormContainer">
            <h2>Book Your Visit</h2>
            <span class="Subtitle">Select a time that works for you.</span>

            <form id="TourForm">
                <!-- User Info -->
                <div class="FormGroup">
                    <div class="InputWithIcon">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" id="FullName" name="FullName" placeholder="Full Name" required>
                    </div>
                </div>

                <div class="FormGroup">
                    <div class="InputWithIcon">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" id="Email" name="Email" placeholder="Email Address" required>
                    </div>
                </div>

                <div class="FormGroup">
                    <div class="InputWithIcon">
                        <i class="fa-solid fa-phone"></i>
                        <input type="tel" id="Phone" name="Phone" placeholder="Phone Number" required>
                    </div>
                </div>

                <!-- Location Select -->
                <div class="FormGroup">
                    <label class="SectionLabel">Select Location</label>
                    <div class="InputWithIcon">
                        <i class="fa-solid fa-location-dot"></i>
                        <select id="LocationId" name="LocationId" required onchange="FetchAvailability()">
                            <option value="" disabled selected>Choose a Studio...</option>
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?= $loc['id'] ?>"><?= htmlspecialchars($loc['Name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Date Picker -->
                <div class="FormGroup">
                    <label class="SectionLabel">Select Date</label>
                    <div class="DateGrid" id="DateContainer">
                        <!-- Filled via JS -->
                    </div>
                    <input type="hidden" id="SelectedDate" name="TourDate" required>
                </div>

                <!-- Time Slots -->
                <div class="FormGroup">
                    <label class="SectionLabel">Available Times</label>
                    <div class="TimeGrid" id="TimeContainer">
                        <div style="grid-column: 1/-1; text-align: center; color: #555; font-size: 0.9rem;">
                            Select a date and location to see times.
                        </div>
                    </div>
                    <input type="hidden" id="SelectedSlotId" name="SlotId" required>
                </div>

                <button type="submit" class="BtnSubmit">
                    Schedule My Tour <i class="fa-solid fa-arrow-right"></i>
                </button>

                <div class="LoginHint">
                    Already a member? <a href="Login.php">Log in here</a>
                </div>
            </form>
        </div>
    </div>

    <!-- JS Logic -->
    <script src="js/tour.js"></script>

</body>
</html>
