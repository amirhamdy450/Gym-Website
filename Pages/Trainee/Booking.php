<?php
require_once '../../Includes/Auth.php';
require_once '../../Includes/DB.php';

// 1. Auth Check
AuthRequireRole('Trainee');
$UserId = AuthId();
$UserName = $_SESSION['user_name'];

// 2. Fetch User Credits
$CreditStmt = $pdo->prepare("
    SELECT 
        s.BalancePrivateSessions as PlanCredits,
        u.WalletPrivateSessions as WalletCredits
    FROM users u
    LEFT JOIN subscriptions s ON u.id = s.UserId AND s.Status = 'Active'
    WHERE u.id = ?
");
$CreditStmt->execute([$UserId]);
$Credits = $CreditStmt->fetch(PDO::FETCH_ASSOC);

$PlanCredits = $Credits['PlanCredits'] ?? 0;
$WalletCredits = $Credits['WalletCredits'] ?? 0;

// 3. Fetch Instructors
$InstructorStmt = $pdo->query("SELECT id, FirstName, LastName, ProfileImage FROM users WHERE Role = 'Instructor'");
$Instructors = $InstructorStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Private Session | EpixGym</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/booking.css">
    <script>
        // Pass PHP data to JS
        window.UserCredits = {
            plan: <?= $PlanCredits ?>,
            wallet: <?= $WalletCredits ?>
        };
    </script>
</head>

<body>

    <div class="WizardContainer">
        <!-- Header -->
        <div class="WizardHeader">
            <h1>Book Private <span>Session</span></h1>
            <p>Schedule your 1-on-1 training to reach your goals faster.</p>
        </div>

        <!-- Steps -->
        <div class="StepsIndicator">
            <div class="StepInfo active" id="step1">
                <div class="StepCircle">1</div>
                <div class="StepLabel">Trainer</div>
            </div>
            <div class="StepInfo" id="step2">
                <div class="StepCircle">2</div>
                <div class="StepLabel">Date & Time</div>
            </div>
            <div class="StepInfo" id="step3">
                <div class="StepCircle">3</div>
                <div class="StepLabel">Confirm</div>
            </div>
        </div>

        <!-- Step 1: Select Trainer -->
        <div class="StepContent active" id="content1">
            <h3 class="SectionTitle">Select Your Trainer</h3>
            <div class="TrainerGrid">
                <?php foreach ($Instructors as $Instr): ?>
                    <?php
                    $Img = $Instr['ProfileImage'] ? $Instr['ProfileImage'] : '../../Imgs/default_avatar.png';
                    // Use Lorem Picsum or similar if no image, or a placeholder div
                    // For now, let's use a placeholder style if missing
                    ?>
                    <div class="TrainerCard" data-id="<?= $Instr['id'] ?>" data-name="<?= htmlspecialchars($Instr['FirstName']) ?>">
                        <img src="<?= htmlspecialchars($Img) ?>" alt="Avatar" class="TrainerAvatar">
                        <div class="TrainerName"><?= htmlspecialchars($Instr['FirstName'] . ' ' . $Instr['LastName']) ?></div>
                        <div class="TrainerSpecialty">Fitness Specialist</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Step 2: Date & Time -->
        <div class="StepContent" id="content2">
            <div class="DateTimeContainer">
                <div class="DateSection">
                    <h3 class="SectionTitle">Pick a Date</h3>
                    <input type="date" id="bookingDate" min="<?= date('Y-m-d') ?>">
                </div>
                <div class="TimeSection">
                    <h3 class="SectionTitle">Available Slots</h3>
                    <div class="TimeSlotGrid" id="timeSlots">
                        <!-- JS will populate this -->
                        <div class="TimeSlot disabled">Select Date First</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Confirmation -->
        <div class="StepContent" id="content3">
            <h3 class="SectionTitle">Confirm Details</h3>
            <div class="SummaryBox">
                <div class="SummaryItem">
                    <span class="SummaryLabel">Trainer</span>
                    <span class="SummaryValue" id="summaryTrainer">-</span>
                </div>
                <div class="SummaryItem">
                    <span class="SummaryLabel">Date</span>
                    <span class="SummaryValue" id="summaryDate">-</span>
                </div>
                <div class="SummaryItem">
                    <span class="SummaryLabel">Time</span>
                    <span class="SummaryValue" id="summaryTime">-</span>
                </div>
                <div class="SummaryItem">
                    <span class="SummaryLabel">Credit Status</span>
                    <span class="SummaryValue" id="creditBalance">Checking...</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="WizardActions">
            <button class="Btn Secondary" id="btnBack" style="display:none;">Back</button>
            <button class="Btn Primary" id="btnNext" disabled>Next Step</button>
            <button class="Btn Primary" id="btnConfirm" style="display:none;">Confirm Booking</button>
        </div>
    </div>

    <script src="../../js/booking.js"></script>

</body>

</html>