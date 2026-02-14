<?php
// Pages/Trainee/Dashboard.php
require_once '../../Includes/Auth.php';
require_once '../../Includes/DB.php';

// Check Auth & Role
if (!AuthIsLoggedIn()) {
    header("Location: ../../Login.php");
    exit;
}

if ($_SESSION['user_role'] !== 'Trainee') {
    header("Location: ../../index.php");
    exit;
}

$UserName = $_SESSION['user_name'] ?? 'Athlete';
$FirstName = explode(' ', $UserName)[0]; // Get just the first name for the header

$UserId = AuthId();

// 1. Fetch User Stats (Latest 2 for trend)
$Stmt = $pdo->prepare("SELECT * FROM userphysicalstats WHERE UserId = ? ORDER BY RecordedAt DESC LIMIT 2");
$Stmt->execute([$UserId]);
$StatsHistory = $Stmt->fetchAll(PDO::FETCH_ASSOC);

$CurrentWeight = $StatsHistory[0]['Weight'] ?? 75; // Default if no data
$CurrentFat = $StatsHistory[0]['BodyFat'] ?? 15;
$PrevWeight = $StatsHistory[1]['Weight'] ?? $CurrentWeight;
$PrevFat = $StatsHistory[1]['BodyFat'] ?? $CurrentFat;

$WeightChange = $CurrentWeight - $PrevWeight; // Negative means loss (good)
$FatChange = $CurrentFat - $PrevFat;

// 1.5 Fetch User Wallet Balance
$UserWalletStmt = $pdo->prepare("SELECT WalletPrivateSessions FROM users WHERE id = ?");
$UserWalletStmt->execute([$UserId]);
$UserWallet = $UserWalletStmt->fetchColumn() ?: 0;

// 2. Fetch Private Sessions (Confirmed)
$PrivateStmt = $pdo->prepare("
    SELECT ps.id, ps.Category as Title, ps.StartTime, ps.DurationMinutes, ps.Status, 
           'Private' as Type, CONCAT(u.FirstName, ' ', u.LastName) as InstructorName, 'Zone 4' as Location
    FROM privatesessions ps
    JOIN users u ON ps.InstructorId = u.id
    WHERE ps.UserId = ? AND ps.Status = 'Confirmed' AND ps.StartTime >= NOW()
");
$PrivateStmt->execute([$UserId]);
$PrivateSessions = $PrivateStmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Fetch Program Bookings (Confirmed)
$ProgramStmt = $pdo->prepare("
    SELECT pb.id, gp.Title, s.StartTime, s.DurationMinutes, pb.Status, 
           'Program' as Type, CONCAT(u.FirstName, ' ', u.LastName) as InstructorName, gp.Room as Location
    FROM programbookings pb
    JOIN programsessions s ON pb.SessionId = s.id
    JOIN groupprograms gp ON s.ProgramId = gp.id
    LEFT JOIN users u ON s.InstructorId = u.id
    WHERE pb.UserId = ? AND pb.Status = 'Confirmed' AND s.StartTime >= NOW()
");
$ProgramStmt->execute([$UserId]);
$ProgramSessions = $ProgramStmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Sort each independently
usort($PrivateSessions, function ($a, $b) {
    return strtotime($a['StartTime']) - strtotime($b['StartTime']);
});
usort($ProgramSessions, function ($a, $b) {
    return strtotime($a['StartTime']) - strtotime($b['StartTime']);
});

// 5. Fetch Active Subscription
$SubStmt = $pdo->prepare("
    SELECT s.Status, s.EndDate, m.Name as PlanName, s.BalancePrivateSessions -- Ensure we fetch balance
    FROM subscriptions s
    JOIN memberships m ON s.MembershipId = m.id
    WHERE s.UserId = ? AND s.Status = 'Active'
    LIMIT 1
");
$SubStmt->execute([$UserId]);
$Subscription = $SubStmt->fetch(PDO::FETCH_ASSOC);

if ($Subscription) {
    $PlanName = $Subscription['PlanName'] ?? 'No Plan';
    $SubStatus = $Subscription['Status'] ?? 'Inactive';
    $RenewsDate = $Subscription['EndDate'] ? date('M d, Y', strtotime($Subscription['EndDate'])) : 'N/A';
    $SubBalance = $Subscription['BalancePrivateSessions'] ?? 0;
} else {
    $PlanName = 'No Plan';
    $SubStatus = 'Inactive';
    $RenewsDate = 'N/A';
    $SubBalance = 0;
}

// 6. Simulate Live Occupancy (Peak hours: 17:00 - 20:00)
$Hour = (int)date('H');
if ($Hour >= 17 && $Hour <= 20) {
    $Occupancy = rand(75, 95);
    $TrafficStatus = "Very Busy";
} elseif ($Hour >= 7 && $Hour <= 9) {
    $Occupancy = rand(50, 70);
    $TrafficStatus = "Moderate";
} else {
    $Occupancy = rand(10, 30);
    $TrafficStatus = "Quiet";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Epix Gym</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/trainee_dashboard.css">
    <link rel="stylesheet" href="../../css/booking.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // Inject User Credits for JS
        window.UserCredits = {
            plan: <?= $SubBalance ?>,
            wallet: <?= $UserWallet ?>
        };
    </script>

<body class="DashboardBody">

    <!-- Navigation -->
    <?php
    $Prefix = '../../';
    include '../../Includes/NavBar.php';
    ?>

    <div class="DashboardWrapper">

        <!-- Header Section -->
        <header class="DashHeader">
            <div>
                <h1>Ready to crush it, <span class="Highlight"><?= htmlspecialchars($FirstName) ?>?</span></h1>
                <div class="HeaderMeta">
                    <span class="StatusPill"><span class="Dot"></span> Gym is currently <?= strtolower($TrafficStatus) ?></span>
                    <span class="LastVisit">Last visit: Yesterday</span>
                </div>
            </div>
        </header>

        <!-- Main Grid Layout -->
        <div class="DashGrid">

            <!-- LEFT COLUMN (Main Stats & Schedule) -->
            <div class="MainCol">

                <!-- Progress Section -->
                <section class="Card ProgressCard">
                    <div class="CardHeader">
                        <div>
                            <h3><i class="fa-solid fa-chart-line"></i> My Progress</h3>
                            <span class="SubTitle">Tracking Weight & Body Fat</span>
                        </div>
                        <div class="TimeframeToggles">
                            <button class="ToggleBtn" data-range="1m">1M</button>
                            <button class="ToggleBtn active" data-range="3m">3M</button>
                            <button class="ToggleBtn" data-range="6m">6M</button>
                            <button class="ToggleBtn" data-range="all">ALL</button>
                        </div>
                    </div>

                    <div class="ChartsContainer">
                        <!-- Weight Chart -->
                        <div class="ChartCol">
                            <div class="ChartHeader">
                                <span class="Label">WEIGHT</span>
                                <span class="Value"><span id="valWeightNow"><?= $CurrentWeight ?></span> <small>kg</small></span>
                            </div>
                            <div class="ChartGraphic WeightChart">
                                <svg viewBox="0 0 100 40" preserveAspectRatio="none">
                                    <path id="pathWeight" d="M0,20 Q50,30 100,35" stroke="#FF0055" stroke-width="2" fill="none" />
                                </svg>
                                <div id="dotWeight" class="ChartDot"></div>
                                <div class="ChartLabels">
                                    <span>START</span>
                                    <span id="lblWeightChange" class="Change <?= $WeightChange <= 0 ? 'negative' : 'positive' ?>">
                                        <?= $WeightChange > 0 ? '+' : '' ?><?= number_format($WeightChange, 1) ?>KG
                                    </span>
                                    <span>NOW</span>
                                </div>
                            </div>
                        </div>

                        <!-- Body Fat Chart -->
                        <div class="ChartCol">
                            <div class="ChartHeader">
                                <span class="Label">BODY FAT</span>
                                <span class="Value"><span id="valFatNow"><?= $CurrentFat ?></span> <small>%</small></span>
                            </div>
                            <div class="ChartGraphic FatChart">
                                <svg viewBox="0 0 100 40" preserveAspectRatio="none">
                                    <path id="pathFat" d="M0,10 Q50,15 100,25" stroke="#ccc" stroke-width="2" fill="none" />
                                </svg>
                                <div id="dotFat" class="ChartDot"></div>
                                <div class="ChartLabels">
                                    <span>START</span>
                                    <span id="lblFatChange" class="Change <?= $FatChange <= 0 ? 'positive' : 'negative' ?>">
                                        <?= $FatChange > 0 ? '+' : '' ?><?= number_format($FatChange, 1) ?>%
                                    </span>
                                    <span>NOW</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Private Sessions Card -->
                <section class="Card SessionsCard PrivateSessionsCard">
                    <div class="CardHeader">
                        <div>
                            <h3><i class="fa-solid fa-user-ninja"></i> Private Sessions</h3>
                            <span class="SubTitle">1-on-1 Coaching</span>
                        </div>
                        <div class="HeaderActions">
                            <button onclick="BookingWizard.open()" class="LinkSmall" style="background:none;border:none;cursor:pointer;color:white;">BOOK NEW</button>
                        </div>
                    </div>

                    <div class="SessionList">
                        <?php if (empty($PrivateSessions)): ?>
                            <div class="SessionRow">
                                <p style="color: #666; font-style: italic;">No private sessions booked.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($PrivateSessions as $Session):
                                $Date = new DateTime($Session['StartTime']);
                                $DayName = strtoupper($Date->format('D'));
                                $Time = $Date->format('H:i');
                            ?>
                                <div class="SessionRow PrivateRow" onclick="SessionModal.open(<?= htmlspecialchars(json_encode([
                                                                                                    "id" => $Session["id"],
                                                                                                    "Title" => $Session["Title"],
                                                                                                    "StartTime" => $Session["StartTime"],
                                                                                                    "DurationMinutes" => $Session["DurationMinutes"],
                                                                                                    "InstructorName" => $Session["InstructorName"],
                                                                                                    "Status" => "Confirmed",
                                                                                                    "Location" => "Private Studio",
                                                                                                    "Notes" => "",
                                                                                                    "Category" => "Private Session",
                                                                                                    "Type" => "Private"
                                                                                                ]), ENT_QUOTES, 'UTF-8') ?>)">
                                    <div class="DateBox">
                                        <span class="Label"><?= $DayName ?></span>
                                        <span class="Time"><?= $Time ?></span>
                                    </div>
                                    <div class="SessionDetails">
                                        <h4><?= htmlspecialchars($Session['Title']) ?></h4>
                                        <div class="Meta">
                                            <span><i class="fa-solid fa-user"></i> Coach <?= htmlspecialchars($Session['InstructorName']) ?></span>
                                            <span><i class="fa-regular fa-clock"></i> <?= $Session['DurationMinutes'] ?>m</span>
                                        </div>
                                    </div>
                                    <div class="SessionStatus">
                                        <span class="StatusText text-red"><span class="Dot red"></span> AWAITING CHECK-IN</span>
                                        <button class="BtnSm">DETAILS</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Programs Card -->
                <section class="Card SessionsCard ProgramsCard" style="margin-top: 20px;">
                    <div class="CardHeader">
                        <div>
                            <h3><i class="fa-solid fa-users"></i> My Programs</h3>
                            <span class="SubTitle">Group Classes & Events</span>
                        </div>
                        <div class="HeaderActions">
                            <a href="#" class="LinkSmall">BROWSE SCHEDULE</a>
                        </div>
                    </div>

                    <div class="SessionList">
                        <?php if (empty($ProgramSessions)): ?>
                            <div class="SessionRow">
                                <p style="color: #666; font-style: italic;">No active program enrollments.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($ProgramSessions as $Session):
                                $Date = new DateTime($Session['StartTime']);
                                $DayName = strtoupper($Date->format('D'));
                                $Time = $Date->format('H:i');
                            ?>
                                <div class="SessionRow ProgramRow">
                                    <div class="DateBox ProgramDateBox">
                                        <span class="Label"><?= $DayName ?></span>
                                        <span class="Time"><?= $Time ?></span>
                                    </div>
                                    <div class="SessionDetails">
                                        <h4><?= htmlspecialchars($Session['Title']) ?></h4>
                                        <div class="Meta">
                                            <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($Session['Location']) ?></span>
                                            <?php if ($Session['InstructorName']): ?>
                                                <span><i class="fa-solid fa-chalkboard-user"></i> <?= htmlspecialchars($Session['InstructorName']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="SessionStatus">
                                        <span class="StatusText" style="color: #00ff88;">CONFIRMED</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </section>

            </div>

            <!-- RIGHT COLUMN (Plan & Actions) -->
            <div class="SideCol">

                <!-- Membership Card -->
                <div class="MembershipCard">
                    <div class="CardTop">
                        <span class="Label">CURRENT PLAN</span>
                        <h2><?= htmlspecialchars($PlanName) ?> <i>MEMBERSHIP</i></h2>
                        <?php if ($SubStatus === 'Active'): ?>
                            <div class="IconCheck"><i class="fa-solid fa-check"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="CardDetails">
                        <div class="DetailRow">
                            <span>Status</span>
                            <?php if ($SubStatus === 'Active'): ?>
                                <span class="StatusActive"><span class="Dot green"></span> Active</span>
                            <?php else: ?>
                                <span class="StatusInactive" style="color: #666;"><span class="Dot red"></span> Inactive</span>
                            <?php endif; ?>
                        </div>
                        <div class="DetailRow">
                            <span>Renews On</span>
                            <strong><?= $RenewsDate ?></strong>
                        </div>
                    </div>
                    <button class="BtnManage">MANAGE PLAN</button>
                </div>

                <div class="SectionLabel">QUICK ACTIONS</div>

                <!-- Action: Book Private -->
                <div class="ActionCard LargeAction">
                    <div class="ActionContent">
                        <h3>BOOK PRIVATE TRAINING</h3>
                        <p>1-on-1 with elite coaches</p>
                        <button onclick="BookingWizard.open()" class="BtnActionRed">BOOK NOW</button>
                    </div>
                    <img src="../../Imgs/private_session_card_bg.png" alt="Trainer" class="ActionImg"> <!-- Placeholder, CSS handles gradient -->
                </div>

                <!-- Action: Supplements -->
                <div class="ActionCard SmallAction">
                    <div class="ActionContent">
                        <h3>ORDER SUPPLEMENTS</h3>
                        <p>Refuel for your next session</p>
                        <button class="BtnActionRed">GO TO SHOP</button>
                    </div>
                </div>

                <!-- Occupancy -->
                <div class="OccupancyWidget">
                    <div class="OccHeader">
                        <span><i class="fa-solid fa-signal"></i> Live Occupancy</span>
                        <span class="StatusTag">OPEN NOW</span>
                    </div>
                    <div class="OccValue">
                        <?= $Occupancy ?>%
                        <small><?= $TrafficStatus ?></small>
                    </div>
                    <div class="OccBar">
                        <div class="Fill" style="width: <?= $Occupancy ?>%;"></div>
                    </div>
                    <div class="OccHours">
                        <div class="HourRow">
                            <span>Main Gym</span>
                            <span>05:00 - 23:00</span>
                        </div>
                        <div class="HourRow">
                            <span>Pool & Sauna</span>
                            <span>06:00 - 22:00</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- BOOKING MODAL -->
    <div id="BookingModal" class="ModalOverlay">
        <div class="WizardContainer">
            <button id="btnCloseModal" class="BtnCloseModal"><i class="fa-solid fa-xmark"></i></button>

            <div class="WizardHeader">
                <h1>Book <span class="Highlight">Private Session</span></h1>
                <p>Maximize your gains with 1-on-1 coaching</p>
            </div>

            <div class="StepsIndicator">
                <div class="StepInfo active">
                    <div class="StepCircle">1</div>
                    <span class="StepLabel">Trainer</span>
                </div>
                <div class="StepInfo">
                    <div class="StepCircle">2</div>
                    <span class="StepLabel">Schedule</span>
                </div>
                <div class="StepInfo">
                    <div class="StepCircle">3</div>
                    <span class="StepLabel">Confirm</span>
                </div>
            </div>

            <div class="WizardContent">
                <!-- STEP 1: TRAINER -->
                <div class="StepContent active" id="Step1">
                    <h3 style="margin-bottom: 20px;">Choose your Expert</h3>
                    <div class="TrainerGrid">
                        <!-- Dynamic fetch would be better, but hardcoded for now based on DB -->
                        <?php
                        $InstrStmt = $pdo->query("SELECT id, CONCAT(FirstName, ' ', LastName) as Name FROM users WHERE Role = 'Instructor'");
                        while ($Instr = $InstrStmt->fetch(PDO::FETCH_ASSOC)):
                            $Avatar = "../../imgs/instructor_avatar_" . ($Instr['id'] % 2 == 0 ? 'male' : 'female') . ".png";
                        ?>
                            <div class="TrainerCard" data-id="<?= $Instr['id'] ?>" data-name="<?= htmlspecialchars($Instr['Name']) ?>">
                                <div style="width:60px; height:60px; background:#444; border-radius:50%; margin:0 auto 10px; display:flex; align-items:center; justify-content:center; font-size:1.5rem;">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                                <h4 style="margin:0; font-size:1rem;"><?= htmlspecialchars($Instr['Name']) ?></h4>
                                <span style="font-size:0.8rem; color:#888;">Elite Coach</span>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- STEP 2: SCHEDULE -->
                <div class="StepContent" id="Step2">
                    <h3 style="margin-bottom: 5px;">Build your Schedule</h3>
                    <p style="color:#888; margin-bottom: 20px; font-size:0.9rem;">
                        Toggle between specific dates or a recurring weekly schedule.
                    </p>

                    <!-- 1. Segmented Control / Toggle -->
                    <div class="SegmentedControl">
                        <div class="SegmentOption active" data-mode="single">Specific Dates</div>
                        <div class="SegmentOption" data-mode="recurring">Repeat Weekly</div>
                    </div>

                    <!-- 2. Date Selection Views -->
                    <div id="dateViewContainer" style="margin-bottom:20px;">

                        <!-- Single Mode: Horizontal Date Strip with Scroll Controls -->
                        <!-- ADDED ID dateScrollWrapper HERE -->
                        <div id="dateScrollWrapper" class="DateScrollWrapper">
                            <button id="btnScrollLeft" class="ScrollBtn"><i class="fa fa-chevron-left"></i></button>
                            <div id="dateStrip" class="DateStripContainer">
                                <!-- JS Injected Cards -->
                            </div>
                            <button id="btnScrollRight" class="ScrollBtn"><i class="fa fa-chevron-right"></i></button>
                        </div>

                        <!-- Recurring Mode: Day Grid -->
                        <div id="dayGrid" class="DayGridContainer" style="display:none;">
                            <!-- JS Injected Circles -->
                        </div>

                        <!-- Consistency Warning (Dynamic) -->
                        <div id="consistencyWarning" class="ConsistencyWarning" style="display:none;">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <div>
                                <strong>Consistency Check</strong><br>
                                <span style="opacity:0.9;">Gaps larger than 2 weeks might impact your training consistency.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden Inputs -->
                    <input type="date" id="bookingDate" style="position:absolute; opacity:0; z-index:-1; width:0; height:0;">
                    <input type="checkbox" id="checkRepeat" style="display:none;">

                    <!-- Weeks Input (Visible only in Recurring) -->
                    <div id="weeksInputContainer" style="display:none; text-align:center; margin-bottom:20px; background:#1a1a1a; padding:10px; border-radius:12px; border:1px solid #333;">
                        <label style="color:#888; margin-right:10px;">For how many weeks?</label>
                        <input type="number" id="inputWeeks" value="4" min="2" max="12" style="background:#333; border:none; color:white; padding:5px 10px; border-radius:6px; width:60px; text-align:center;">
                    </div>

                    <!-- 3. Time Slots -->
                    <div>
                        <div class="TimeSlotGrid" id="timeSlots" style="display:grid; grid-template-columns: repeat(5, 1fr); gap:8px;">
                            <p style="color:#666; grid-column: span 5; text-align:center; padding:20px;">Select a date to view times.</p>
                        </div>
                    </div>

                    <!-- 4. Selected Slots Cart -->
                    <div id="selectionCart" class="SelectionCart">
                        <!-- JS Injected Chips -->
                    </div>
                </div>

                <!-- STEP 3: CONFIRM -->
                <div class="StepContent" id="Step3">
                    <h3 style="margin-bottom: 20px;">Confirmation</h3>

                    <div style="background:#252528; padding:20px; border-radius:16px; margin-bottom:20px;">
                        <div class="SummaryItem"><span>Coach</span><span class="Highlight" id="summaryTrainer">-</span></div>
                        <div class="SummaryItem"><span>Schedule</span><span class="Highlight" id="summaryDate">-</span></div>
                        <div class="SummaryItem"><span>Total Booking</span><span class="Highlight" id="summaryTime">-</span></div>
                        <div class="SummaryItem"><span>Frequency</span><span class="Highlight" id="summaryRepeat">-</span></div>
                        <div class="SummaryItem" style="margin-top:15px; padding-top:15px; border-top:1px solid #444;">
                            <span style="font-size:1.1rem; font-weight:bold;">Credit Status</span>
                            <div id="creditBalance" style="text-align:right;">Calculating...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GLOBAL WIZARD FOOTER -->
            <div class="WizardFooter" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #333; display: flex; justify-content: space-between; align-items: center;">
                <button id="btnBack" class="BtnSm" style="display:none; background:transparent; border:1px solid #555; color:#aaa;">BACK</button>
                <div style="flex-grow:1;"></div>
                <button id="btnNext" class="BtnActionRed" disabled>NEXT STEP</button>
                <button id="btnConfirm" class="BtnActionRed" style="display:none;">CONFIRM BOOKING</button>
            </div>
        </div>
    </div>
    <!-- SESSION DETAILS MODAL -->
    <div id="SessionDetailsModal" class="ModalOverlay">
        <div class="ModalBox">
            <button class="BtnCloseModal" onclick="SessionModal.close()"><i class="fa-solid fa-xmark"></i></button>

            <div class="ModalHeader">
                <span class="ModalTag" id="modalSessionType">PRIVATE SESSION</span>
                <h2 id="modalSessionTitle">Power Lifting</h2>
                <p id="modalSessionMeta"><i class="fa-regular fa-clock"></i> <span id="modalSessionTime">09:00 - 10:00</span> &bull; <span id="modalSessionDate">Mon, Oct 24</span></p>
            </div>

            <div class="ModalBody">
                <div class="DetailBlock">
                    <label>INSTRUCTOR</label>
                    <div class="InstructorRow">
                        <div class="AvatarSmall" id="modalInstructorAvatar"><i class="fa-solid fa-user"></i></div>
                        <div>
                            <strong id="modalInstructorName">Coach Sarah</strong>
                            <span class="SubText">Elite Trainer</span>
                        </div>
                    </div>
                </div>

                <div class="DetailBlock">
                    <label>LOCATION</label>
                    <p id="modalLocation"><i class="fa-solid fa-location-dot"></i> Zone 4 (Free Weights)</p>
                </div>

                <div class="DetailBlock">
                    <label>NOTES</label>
                    <p id="modalNotes" class="NotesText">Focus on deadlift form and grip strength.</p>
                </div>

                <div class="ActionButtons">
                    <button class="BtnOutline" onclick="SessionModal.cancelSession()">CANCEL SESSION</button>
                    <!-- Reschedule to be implemented later -->
                    <!-- <button class="BtnPrimary" onclick="SessionModal.reschedule()">RESCHEDULE</button> -->
                </div>
            </div>
        </div>
    </div>

    </div>
    <!-- END DashboardWrapper -->

    <script src="../../js/booking.js"></script>
    <script src="../../js/dashboard_charts.js"></script>
</body>

</html>