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

// 1. Fetch User Stats (Latest 2 for trend, All for graph in future)
$Stmt = $pdo->prepare("SELECT * FROM userphysicalstats WHERE UserId = ? ORDER BY RecordedAt DESC LIMIT 2");
$Stmt->execute([$UserId]);
$StatsHistory = $Stmt->fetchAll(PDO::FETCH_ASSOC);

$CurrentWeight = $StatsHistory[0]['Weight'] ?? 75; // Default if no data
$CurrentFat = $StatsHistory[0]['BodyFat'] ?? 15;
$PrevWeight = $StatsHistory[1]['Weight'] ?? $CurrentWeight;
$PrevFat = $StatsHistory[1]['BodyFat'] ?? $CurrentFat;

$WeightChange = $CurrentWeight - $PrevWeight; // Negative means loss (good)
$FatChange = $CurrentFat - $PrevFat;

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
    SELECT s.Status, s.EndDate, m.Name as PlanName 
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
} else {
    $PlanName = 'No Plan';
    $SubStatus = 'Inactive';
    $RenewsDate = 'N/A';
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

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
                            <button class="ToggleBtn">1M</button>
                            <button class="ToggleBtn Active">3M</button>
                            <button class="ToggleBtn">6M</button>
                        </div>
                    </div>

                    <div class="ChartsContainer">
                        <!-- Weight Chart Mock -->
                        <div class="ChartCol">
                            <div class="ChartHeader">
                                <span class="Label">WEIGHT</span>
                                <span class="Value"><?= $CurrentWeight ?> <small>kg</small></span>
                            </div>
                            <!-- Visual representation of a line chart using CSS/SVG placeholder -->
                            <div class="ChartGraphic WeightChart">
                                <svg viewBox="0 0 100 40" preserveAspectRatio="none">
                                    <path d="M0,20 Q50,30 100,35" stroke="#FF0055" stroke-width="2" fill="none" />
                                    <circle cx="100" cy="35" r="2" fill="#FF0055" />
                                </svg>
                                <div class="ChartLabels">
                                    <span>START</span>
                                    <span class="Change <?= $WeightChange <= 0 ? 'negative' : 'positive' ?>">
                                        <?= $WeightChange > 0 ? '+' : '' ?><?= number_format($WeightChange, 1) ?>KG
                                    </span>
                                    <span>NOW</span>
                                </div>
                            </div>
                        </div>

                        <!-- Body Fat Chart Mock -->
                        <div class="ChartCol">
                            <div class="ChartHeader">
                                <span class="Label">BODY FAT</span>
                                <span class="Value"><?= $CurrentFat ?> <small>%</small></span>
                            </div>
                            <div class="ChartGraphic FatChart">
                                <svg viewBox="0 0 100 40" preserveAspectRatio="none">
                                    <path d="M0,10 Q50,15 100,25" stroke="#ccc" stroke-width="2" fill="none" />
                                    <circle cx="100" cy="25" r="2" fill="white" />
                                </svg>
                                <div class="ChartLabels">
                                    <span>START</span>
                                    <span class="Change <?= $FatChange <= 0 ? 'positive' : 'negative' ?>">
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
                            <a href="#" class="LinkSmall">BOOK NEW</a>
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
                                <div class="SessionRow PrivateRow">
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
                                        <button class="BtnSm">RESCHEDULE</button>
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
                        <button class="BtnActionRed">BOOK NOW</button>
                    </div>
                    <img src="../../imgs/trainer-thumb.png" alt="Trainer" class="ActionImg"> <!-- Placeholder, CSS handles gradient -->
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

</body>

</html>