<?php
// Pages/Trainee/Schedule.php
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Schedule | Epix Gym</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../css/style.css"> <!-- Standard styles -->
    <link rel="stylesheet" href="../../css/trainee_dashboard.css"> <!-- Reusing styling -->
    <link rel="stylesheet" href="../../css/schedule.css">
</head>

<body class="DashboardBody">

    <!-- Navigation -->
    <?php
    $Prefix = '../../';
    include '../../Includes/NavBar.php';
    ?>

    <div class="DashboardWrapper">

        <!-- CALENDAR HEADER -->
        <header class="ScheduleHeader" style="border-radius: 20px 20px 0 0; margin-top: 20px;">
            <div class="MonthSelector">
                <button id="btnPrevMonth" class="NavBtn"><i class="fa-solid fa-chevron-left"></i></button>
                <h2 id="currentMonthLabel">October 2026</h2>
                <button id="btnNextMonth" class="NavBtn"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
            <div class="HeaderActions">
                <button id="btnToday" class="BtnToday">Today</button>
            </div>
        </header>

        <!-- CALENDAR GRID -->
        <div class="CalendarWrapper" style="background: #111; border-radius: 0 0 20px 20px; padding: 20px; min-height: 70vh;">
            <div class="WeekHeader">
                <div class="WeekDay">SUN</div>
                <div class="WeekDay">MON</div>
                <div class="WeekDay">TUE</div>
                <div class="WeekDay">WED</div>
                <div class="WeekDay">THU</div>
                <div class="WeekDay">FRI</div>
                <div class="WeekDay">SAT</div>
            </div>
            <div id="calendarGrid" class="CalendarGrid">
                <!-- Days Injected via JS -->
            </div>
        </div>

    </div>

    <!-- SESSION DETAILS MODAL (Reused Structure) -->
    <div id="SessionDetailsModal" class="ModalOverlay">
        <div class="ModalBox">
            <button class="BtnCloseModal" id="btnCloseModal"><i class="fa-solid fa-xmark"></i></button>

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
                    <p id="modalLocation"><i class="fa-solid fa-location-dot"></i> Zone 4 Free Weights</p>
                </div>

                <div class="DetailBlock">
                    <label>NOTES</label>
                    <p id="modalNotes" class="NotesText">Focus on deadlift form.</p>
                </div>

                <div class="ActionButtons">
                    <button class="BtnOutline" id="btnCancelSession">CANCEL SESSION</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- STATE ---
        let currentDate = new Date();
        let currentMonth = currentDate.getMonth() + 1; // 1-12
        let currentYear = currentDate.getFullYear();
        let sessions = [];

        // --- ELEMENTS ---
        const gridEl = document.getElementById('calendarGrid');
        const labelEl = document.getElementById('currentMonthLabel');
        const btnPrev = document.getElementById('btnPrevMonth');
        const btnNext = document.getElementById('btnNextMonth');
        const btnToday = document.getElementById('btnToday');

        // --- LOGIC ---

        function init() {
            console.log("Initializing Schedule...");
            fetchSchedule();
            bindEvents();
        }

        function bindEvents() {
            if (btnPrev) btnPrev.addEventListener('click', () => changeMonth(-1));
            if (btnNext) btnNext.addEventListener('click', () => changeMonth(1));
            if (btnToday) btnToday.addEventListener('click', () => {
                const now = new Date();
                currentMonth = now.getMonth() + 1;
                currentYear = now.getFullYear();
                fetchSchedule();
            });

            // Close Modal Logic
            const modal = document.getElementById('SessionDetailsModal');
            const btnClose = document.getElementById('btnCloseModal');

            const closeModal = () => {
                if (modal) {
                    modal.classList.remove('active');
                    setTimeout(() => modal.style.display = 'none', 300);
                }
            };

            if (btnClose) btnClose.addEventListener('click', closeModal);
            if (modal) modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
        }

        function changeMonth(delta) {
            currentMonth += delta;
            if (currentMonth > 12) {
                currentMonth = 1;
                currentYear++;
            } else if (currentMonth < 1) {
                currentMonth = 12;
                currentYear--;
            }
            fetchSchedule();
        }

        async function fetchSchedule() {
            // Update Label
            if (labelEl) {
                const dateObj = new Date(currentYear, currentMonth - 1, 1);
                labelEl.textContent = dateObj.toLocaleDateString('en-US', {
                    month: 'long',
                    year: 'numeric'
                });
            }

            try {
                const res = await fetch(`../../APIs/GetSchedule.php?month=${currentMonth}&year=${currentYear}`);
                const data = await res.json();

                if (data.success) {
                    sessions = data.sessions;
                    renderCalendar();
                } else {
                    console.error("API Error:", data.error);
                }
            } catch (err) {
                console.error("Fetch Error:", err);
            }
        }

        function renderCalendar() {
            if (!gridEl) return;
            gridEl.innerHTML = '';

            const firstDayOfMonth = new Date(currentYear, currentMonth - 1, 1).getDay(); // 0 (Sun) - 6 (Sat)
            const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();

            // Previous Month Padding
            for (let i = 0; i < firstDayOfMonth; i++) {
                const cell = document.createElement('div');
                cell.className = 'DayCell other-month';
                gridEl.appendChild(cell);
            }

            // Days
            const today = new Date();
            const isCurrentMonth = (today.getMonth() + 1 === currentMonth) && (today.getFullYear() === currentYear);

            for (let day = 1; day <= daysInMonth; day++) {
                const cell = document.createElement('div');
                cell.className = 'DayCell';

                if (isCurrentMonth && day === today.getDate()) {
                    cell.classList.add('today');
                }

                // Date Number
                const num = document.createElement('span');
                num.className = 'DayNumber';
                num.textContent = day;
                cell.appendChild(num);

                // Find Sessions for this day
                const daySessions = sessions.filter(s => {
                    const sDate = new Date(s.StartTime);
                    return sDate.getDate() === day;
                });

                daySessions.forEach(session => {
                    const item = document.createElement('div');
                    item.className = `SessionItem ${session.Type}`; // Private or Program

                    const time = new Date(session.StartTime).toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    });

                    // Escape simple XSS just in case, though handled by Context usually
                    // Helper to safe text
                    const safeTitle = session.Title.replace(/</g, "&lt;").replace(/>/g, "&gt;");

                    item.innerHTML = `<span class="SessionTime">${time}</span> ${safeTitle}`;
                    item.onclick = (e) => {
                        e.stopPropagation();
                        openSessionModal(session);
                    };

                    cell.appendChild(item);
                });

                gridEl.appendChild(cell);
            }
        }

        function openSessionModal(sessionData) {
            // Reusing the same modal logic from Dashboard, but inline here since we don't import booking.js
            console.log("Opening Session:", sessionData);

            const modal = document.getElementById('SessionDetailsModal');
            if (!modal) return;

            // Populate Fields
            const typeEl = document.getElementById('modalSessionType');
            if (typeEl) typeEl.textContent = (sessionData.Category || sessionData.Type || 'Session').toUpperCase();

            const titleEl = document.getElementById('modalSessionTitle');
            if (titleEl) titleEl.textContent = sessionData.Title;

            const dateObj = new Date(sessionData.StartTime);
            const timeStr = dateObj.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
            const dateStr = dateObj.toLocaleDateString('en-US', {
                weekday: 'short',
                month: 'short',
                day: 'numeric'
            });

            const duration = parseInt(sessionData.DurationMinutes) || 60;
            const endDate = new Date(dateObj.getTime() + duration * 60000);
            const endTimeStr = endDate.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });

            const timeEl = document.getElementById('modalSessionTime');
            if (timeEl) timeEl.textContent = `${timeStr} - ${endTimeStr}`;

            const dateEl = document.getElementById('modalSessionDate');
            if (dateEl) dateEl.textContent = dateStr;

            const instrEl = document.getElementById('modalInstructorName');
            if (instrEl) instrEl.textContent = sessionData.InstructorName || 'TBD';

            const locEl = document.getElementById('modalLocation');
            if (locEl) locEl.innerHTML = `<i class="fa-solid fa-location-dot"></i> ${sessionData.Location || 'Main Gym'}`;

            const notesEl = document.getElementById('modalNotes');
            if (notesEl) {
                if (sessionData.Notes) {
                    notesEl.textContent = sessionData.Notes;
                    notesEl.style.display = 'block';
                } else {
                    notesEl.style.display = 'none';
                }
            }

            // Cancel Button Logic
            const btnCancel = document.getElementById('btnCancelSession');
            if (btnCancel) {
                btnCancel.onclick = () => {
                    if (!confirm('Are you sure you want to cancel this session?')) return;

                    fetch('../../APIs/SessionActions.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                action: 'cancel',
                                sessionId: sessionData.id,
                                type: sessionData.Type
                            })
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success) {
                                alert('Session cancelled.');
                                modal.classList.remove('active');
                                modal.style.display = 'none';
                                fetchSchedule(); // Refresh calendar
                            } else {
                                alert('Error: ' + res.error);
                            }
                        });
                };
            }

            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('active'), 10);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', init);
    </script>

</body>

</html>