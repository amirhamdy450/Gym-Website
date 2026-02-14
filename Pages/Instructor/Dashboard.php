<?php
// Pages/Instructor/Dashboard.php
require_once '../../Includes/DB.php';
require_once '../../Includes/Auth.php';
require_once '../../Includes/Memberships.php';

// 1. Verify Role
if (!AuthIsLoggedIn()) {
    header("Location: ../../Login.php");
    exit;
}
if ($_SESSION['user_role'] !== 'Instructor') {
    header("Location: ../../index.php");
    exit;
}

$InstructorId = $_SESSION['user_id'];
$InstructorName = $_SESSION['user_name'];

// 2. Fetch Today's Schedule (Private + Programs)
// A. Private Sessions Today
$PrivateStmt = $pdo->prepare("
    SELECT ps.id, ps.Category as Title, ps.StartTime, ps.DurationMinutes, ps.Status, 
           'Private' as Type, CONCAT(u.FirstName, ' ', u.LastName) as ClientName, u.ProfileImage, u.id as ClientId
    FROM privatesessions ps
    JOIN users u ON ps.UserId = u.id
    WHERE ps.InstructorId = ? 
    AND DATE(ps.StartTime) = CURDATE()
    AND ps.Status = 'Confirmed'
    ORDER BY ps.StartTime ASC
");
$PrivateStmt->execute([$InstructorId]);
$PrivateToday = $PrivateStmt->fetchAll(PDO::FETCH_ASSOC);

// B. Program Sessions Today
$ProgramStmt = $pdo->prepare("
    SELECT s.id, gp.Title, s.StartTime, s.DurationMinutes, 'Program' as Type, 
           (SELECT COUNT(*) FROM programbookings pb WHERE pb.SessionId = s.id AND pb.Status='Confirmed') as HeadCount,
           gp.Capacity, gp.Room
    FROM programsessions s
    JOIN groupprograms gp ON s.ProgramId = gp.id
    WHERE s.InstructorId = ? 
    AND DATE(s.StartTime) = CURDATE()
    ORDER BY s.StartTime ASC
");
$ProgramStmt->execute([$InstructorId]);
$ProgramToday = $ProgramStmt->fetchAll(PDO::FETCH_ASSOC);

// Merge & Sort Schedule
$TodaySchedule = array_merge($PrivateToday, $ProgramToday);
usort($TodaySchedule, function ($a, $b) {
    return strtotime($a['StartTime']) - strtotime($b['StartTime']);
});

// 3. Fetch Pending Approvals (Private Sessions)
$PendingStmt = $pdo->prepare("
    SELECT ps.id, ps.Category, ps.StartTime, ps.DurationMinutes, 
           CONCAT(u.FirstName, ' ', u.LastName) as ClientName, u.ProfileImage
    FROM privatesessions ps
    JOIN users u ON ps.UserId = u.id
    WHERE ps.InstructorId = ? AND ps.Status = 'Pending'
    ORDER BY ps.StartTime ASC
");
$PendingStmt->execute([$InstructorId]);
$PendingRequests = $PendingStmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Fetch My Roster (Distinct Trainees)
$RosterStmt = $pdo->prepare("
    SELECT DISTINCT u.id, CONCAT(u.FirstName, ' ', u.LastName) as Name, u.FitnessGoals, u.ProfileImage
    FROM users u
    JOIN privatesessions ps ON u.id = ps.UserId
    WHERE ps.InstructorId = ?
    LIMIT 5
");
$RosterStmt->execute([$InstructorId]);
$MyRoster = $RosterStmt->fetchAll(PDO::FETCH_ASSOC);

// 5. Program Performance Stats (Real Data)
// Get programs where this instructor has sessions
$ProgStatsStmt = $pdo->prepare("
    SELECT gp.Title, gp.Capacity, 
           COUNT(DISTINCT ps.id) as TotalSessions,
           (SELECT COUNT(*) FROM programbookings pb JOIN programsessions ps2 ON pb.SessionId = ps2.id WHERE ps2.InstructorId = ? AND ps2.ProgramId = gp.id) as TotalBookings
    FROM groupprograms gp
    JOIN programsessions ps ON gp.id = ps.ProgramId
    WHERE ps.InstructorId = ?
    GROUP BY gp.id, gp.Title, gp.Capacity
");
$ProgStatsStmt->execute([$InstructorId, $InstructorId]);
$ProgramStats = $ProgStatsStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate efficiency based on real bookings vs capacity (if any)
if (!empty($ProgramStats)) {
    $TotalCap = 0;
    $TotalBooked = 0;
    foreach ($ProgramStats as $P) {
        $TotalCap += ($P['Capacity'] * $P['TotalSessions']);
        $TotalBooked += $P['TotalBookings'];
    }
    $EfficiencyPct = $TotalCap > 0 ? round(($TotalBooked / $TotalCap) * 100) : 0;
    $CompletionRate = $EfficiencyPct . "%"; // Reusing variable for UI
} else {
    $CompletionRate = "N/A";
}

// 6. Weekly Workload (Next 7 Days)
$WorkloadQuery = "
    SELECT DATE(StartTime) as Date, COUNT(*) as Count 
    FROM (
        SELECT StartTime, InstructorId FROM privatesessions WHERE Status='Confirmed'
        UNION ALL
        SELECT StartTime, InstructorId FROM programsessions
    ) as AllSessions
    WHERE InstructorId = ? 
    AND DATE(StartTime) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(StartTime)
";
$WorkloadStmt = $pdo->prepare($WorkloadQuery);
$WorkloadStmt->execute([$InstructorId]);
$WorkloadData = $WorkloadStmt->fetchAll(PDO::FETCH_KEY_PAIR); // [Date => Count]

// Fill empty days for chart
$Next7Days = [];
for ($i = 0; $i < 7; $i++) {
    $DayDate = date('Y-m-d', strtotime("+$i days"));
    $DayName = date('D', strtotime("+$i days"));
    $Next7Days[] = [
        'Day' => $DayName,
        'Count' => $WorkloadData[$DayDate] ?? 0,
        'IsToday' => ($i === 0)
    ];
}

// 7. Client Progress Highlights
$ProgressStmt = $pdo->prepare("
    SELECT u.FirstName, u.LastName, ups.Weight, ups.BodyFat, ups.RecordedAt
    FROM userphysicalstats ups
    JOIN users u ON ups.UserId = u.id
    JOIN privatesessions ps ON u.id = ps.UserId
    WHERE ps.InstructorId = ?
    ORDER BY ups.RecordedAt DESC
    LIMIT 3
");
$ProgressStmt->execute([$InstructorId]);
$ClientProgress = $ProgressStmt->fetchAll(PDO::FETCH_ASSOC);
// 6. Weekly Workload (Next 7 Days)
$WorkloadQuery = "
    SELECT DATE(StartTime) as Date, COUNT(*) as Count 
    FROM (
        SELECT StartTime, InstructorId FROM privatesessions WHERE Status='Confirmed'
        UNION ALL
        SELECT StartTime, InstructorId FROM programsessions
    ) as AllSessions
    WHERE InstructorId = ? 
    AND DATE(StartTime) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(StartTime)
";
$WorkloadStmt = $pdo->prepare($WorkloadQuery);
$WorkloadStmt->execute([$InstructorId]);
$WorkloadData = $WorkloadStmt->fetchAll(PDO::FETCH_KEY_PAIR); // [Date => Count]

// Fill empty days for chart
$Next7Days = [];
for ($i = 0; $i < 7; $i++) {
    $DayDate = date('Y-m-d', strtotime("+$i days"));
    $DayName = date('D', strtotime("+$i days"));
    $Next7Days[] = [
        'Day' => $DayName,
        'Count' => $WorkloadData[$DayDate] ?? 0,
        'IsToday' => ($i === 0)
    ];
}

// 7. Client Progress Highlights
$ProgressStmt = $pdo->prepare("
    SELECT u.FirstName, u.LastName, ups.Weight, ups.BodyFat, ups.RecordedAt
    FROM userphysicalstats ups
    JOIN users u ON ups.UserId = u.id
    JOIN privatesessions ps ON u.id = ps.UserId
    WHERE ps.InstructorId = ?
    ORDER BY ups.RecordedAt DESC
    LIMIT 3
");
$ProgressStmt->execute([$InstructorId]);
$ClientProgress = $ProgressStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard | EpixGym Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/instructor_dashboard.css">
    <!-- Quick Inline for Avatars if no image -->
    <style>
        .AvatarPlaceholder {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #333;
            color: #fff;
            font-weight: bold;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header class="DashHeader">
        <div>
            <h1>Instructor Dashboard</h1>
            <div class="SubText">Welcome back, Coach <?= htmlspecialchars($InstructorName) ?>.</div>
        </div>
        <button class="BtnNewSession">
            <i class="fa-solid fa-plus"></i> New Session
        </button>
    </header>

    <div class="DashGrid">

        <!-- LEFT COLUMN: SCHEDULE & STATS -->
        <div class="MainCol">

            <!-- Today's Schedule -->
            <section class="Card">
                <div class="CardHeader">
                    <div class="CardTitle">
                        <i class="fa-regular fa-calendar"></i> Today's Schedule
                    </div>
                    <a href="#" class="LinkAction">View All <i class="fa-solid fa-chevron-right"></i></a>
                </div>

                <div class="Timeline">
                    <?php if (empty($TodaySchedule)): ?>
                        <div style="color: #666; font-style: italic; padding: 20px;">No sessions scheduled for today. Enjoy your day off!</div>
                    <?php else: ?>
                        <?php foreach ($TodaySchedule as $Session):
                            $Time = date('h:i A', strtotime($Session['StartTime']));
                            $IsPrivate = ($Session['Type'] === 'Private');
                            $Title = $IsPrivate ? $Session['ClientName'] : $Session['Title'];
                            $SubInfo = $IsPrivate ? 'Goal: ' . $Session['Title'] : 'Capacity: ' . $Session['HeadCount'] . '/' . $Session['Capacity'];
                            $TagClass = $IsPrivate ? 'Private' : 'Group';
                            $TagLabel = $IsPrivate ? 'PRIVATE' : 'GROUP';
                        ?>
                            <div class="TimelineItem">
                                <div class="TimelineDot"></div>
                                <div class="TimelineContent">
                                    <div class="SessionInfo">
                                        <?php if ($IsPrivate): ?>
                                            <div class="UserAvatar AvatarPlaceholder">
                                                <?= strtoupper(substr($Session['ClientName'], 0, 1)) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="UserAvatar AvatarPlaceholder" style="background: #2a2a2a; color: var(--accent-red);">
                                                <i class="fa-solid fa-users"></i>
                                            </div>
                                        <?php endif; ?>

                                        <div class="SessionDetails">
                                            <h4><?= htmlspecialchars($Title) ?> <span class="Tag <?= $TagClass ?>"><?= $TagLabel ?></span></h4>
                                            <div class="Meta">
                                                <span><?= htmlspecialchars($SubInfo) ?></span>
                                                <span style="color: var(--accent-red);">• <?= $Time ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="BtnAction">Confirm Session</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Weekly Workload & Client Wins Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">

                <!-- Weekly Workload -->
                <section class="Card" style="margin-bottom: 0;">
                    <div class="CardTitle"><i class="fa-solid fa-chart-simple"></i> Workload</div>
                    <div class="WorkloadChart">
                        <?php
                        $MaxLoad = max(array_column($Next7Days, 'Count')) ?: 1; // Avoid div/0
                        foreach ($Next7Days as $Day):
                            $Height = round(($Day['Count'] / $MaxLoad) * 80); // Max 80% height
                            $Height = max($Height, 4); // Min height
                        ?>
                            <div class="WorkloadDay">
                                <div class="WorkloadBar <?= $Day['IsToday'] ? 'Today' : '' ?>" style="height: <?= $Height ?>%;"></div>
                                <span class="WorkloadLabel"><?= $Day['Day'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="SubText" style="text-align: center; margin-top: 15px; font-size: 0.8rem;">
                        Total <?= array_sum(array_column($Next7Days, 'Count')) ?> sessions next 7 days.
                    </div>
                </section>

                <!-- Client Progress -->
                <section class="Card" style="margin-bottom: 0;">
                    <div class="CardTitle"><i class="fa-solid fa-bolt"></i> Recent Wins</div>
                    <?php if (empty($ClientProgress)): ?>
                        <div style="color: #666; font-style: italic; padding: 20px 0;">No recent client updates.</div>
                    <?php else: ?>
                        <div style="margin-top: 20px;">
                            <?php foreach ($ClientProgress as $Prog): ?>
                                <div class="ProgressItem">
                                    <div class="UserAvatar AvatarPlaceholder" style="width: 35px; height: 35px; font-size: 0.7rem;">
                                        <?= strtoupper(substr($Prog['FirstName'], 0, 1)) ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-size: 0.9rem; font-weight: 600;"><?= htmlspecialchars($Prog['FirstName']) ?></div>
                                        <div style="font-size: 0.75rem; color: #888;">Updated Stats</div>
                                    </div>
                                    <div class="ProgressLevel"><?= floatval($Prog['Weight']) ?>kg</div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

            </div>

            <!-- Active Programs Overview (Visual & Meaningful) -->
            <?php if (!empty($ProgramStats)): ?>
                <section class="Card" style="margin-top: 25px;">
                    <div class="CardTitle" style="margin-bottom: 25px;">
                        <i class="fa-solid fa-layer-group"></i> My Program Performance
                    </div>
                    <div class="ProgramGrid">
                        <?php foreach ($ProgramStats as $Stat):
                            $AvgBookings = $Stat['TotalSessions'] > 0 ? round($Stat['TotalBookings'] / $Stat['TotalSessions'], 1) : 0;
                            $FillRate = ($Stat['Capacity'] > 0) ? round(($AvgBookings / $Stat['Capacity']) * 100) : 0;
                        ?>
                            <div class="ProgramCard">
                                <h4><?= htmlspecialchars($Stat['Title']) ?> <i class="fa-solid fa-dumbbell" style="font-size: 0.9em; color: var(--accent-red); opacity: 0.8;"></i></h4>

                                <div class="ProgramStatRow">
                                    <span>Capacity</span>
                                    <strong><?= $Stat['Capacity'] ?> / class</strong>
                                </div>
                                <div class="ProgramStatRow" style="margin-bottom: 15px;">
                                    <span>Avg. Attendance</span>
                                    <strong><?= $AvgBookings ?> <span style="font-size: 0.8rem; color: #666;">(<?= $FillRate ?>%)</span></strong>
                                </div>

                                <div class="ProgressBar">
                                    <div class="ProgressFill" style="width: <?= $FillRate ?>%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

        </div>

        <!-- RIGHT COLUMN: ROSTER & REQUESTS -->
        <div class="SideCol">

            <!-- My Roster -->
            <section class="Card">
                <div class="CardHeader">
                    <div class="CardTitle">My Roster</div>
                    <a href="#" class="LinkAction">View All</a>
                </div>

                <div class="RosterList">
                    <?php if (empty($MyRoster)): ?>
                        <div style="color: #666; font-style: italic;">No active trainees.</div>
                    <?php else: ?>
                        <?php foreach ($MyRoster as $Trainee):
                            $Goals = json_decode($Trainee['FitnessGoals'] ?? '[]', true);
                            $FirstGoal = $Goals[0] ?? 'General Fitness';
                        ?>
                            <div class="RosterItem">
                                <div class="RosterUser">
                                    <div class="RosterAvatar AvatarPlaceholder">
                                        <?= strtoupper(substr($Trainee['Name'], 0, 1)) ?>
                                    </div>
                                    <div class="RosterInfo">
                                        <h5><?= htmlspecialchars($Trainee['Name']) ?></h5>
                                        <span><?= htmlspecialchars($FirstGoal) ?></span>
                                    </div>
                                </div>
                                <span class="LevelBadge">LVL 4</span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Pending Approvals -->
            <section class="Card" style="background: linear-gradient(145deg, #1e1e1e, #1a1a1a); border-color: rgba(255, 0, 85, 0.2);">
                <div class="CardTitle" style="margin-bottom: 10px;">
                    <i class="fa-solid fa-circle-check"></i> Pending Approvals
                </div>
                <div class="SubText" style="margin-bottom: 20px;">
                    <?= count($PendingRequests) ?> new requests waiting.
                </div>

                <?php if (!empty($PendingRequests)): ?>
                    <button class="BtnAction" style="width: 100%; padding: 12px;">REVIEW QUEUE</button>
                    <div style="margin-top: 15px; font-size: 0.8rem; color: #666;">
                        Latest: <?= htmlspecialchars($PendingRequests[0]['ClientName']) ?>
                    </div>
                <?php else: ?>
                    <button class="BtnGhost" style="width: 100%;" disabled>NO PENDING REQUESTS</button>
                <?php endif; ?>
            </section>

            <!-- Quick Actions -->
            <div class="QuickActionsGrid">
                <div class="QuickActionCard">
                    <i class="fa-solid fa-chart-column"></i>
                    <span>REPORTS</span>
                </div>
                <div class="QuickActionCard">
                    <i class="fa-solid fa-envelope"></i>
                    <span>MESSAGES</span>
                </div>
                <div class="QuickActionCard">
                    <i class="fa-solid fa-dumbbell"></i>
                    <span>PLANS</span>
                </div>
                <div class="QuickActionCard">
                    <i class="fa-solid fa-gear"></i>
                    <span>SETTINGS</span>
                </div>
            </div>

            <!-- Gym Floor Status Removed -->

        </div>

        <!-- Extra Row for Main Column if needed -->

    </div>

    <!-- Program Stats Rendered Above -->

</body>

</html>