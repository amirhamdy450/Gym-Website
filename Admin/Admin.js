/* document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    // Replace '1' with the actual program ID dynamically
    var programID = 1;

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: {
            url: '../Server/APIs/Fetch_Program_Schedule.php?ProgramID=' + programID,  // PHP script to fetch data
            method: 'GET',
            failure: function() {
                alert('There was an error while fetching events!');
            }
        },
        
    });

    calendar.render();
}); */

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var programID = 1;  // Replace with dynamic ID if needed

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',  // Change to timeGridWeek for time display
        events: {
            url: '../Server/APIs/Fetch_Program_Schedule.php?ProgramID=' + programID,
            method: 'GET',
            failure: function() {
                alert('There was an error while fetching events!');
            }
        },
        eventTimeFormat: { // Show time in 24-hour format
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        }
    });

    calendar.render();
});