// js/tour.js

// Init Dates (Next 7 days)
const dateContainer = document.getElementById('DateContainer');
const selectedDateInput = document.getElementById('SelectedDate');
const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];

// Generate Date Pills
const today = new Date();
for (let i = 0; i < 7; i++) {
    const date = new Date(today);
    date.setDate(today.getDate() + i);

    const dayName = days[date.getDay()];
    const dayNum = date.getDate();
    const dateStr = date.toISOString().split('T')[0]; // YYYY-MM-DD

    const pill = document.createElement('div');
    pill.className = 'DatePill';
    if (i === 0) pill.classList.add('Selected'); // Default select today
    pill.innerHTML = `<span>${dayName}</span><strong>${dayNum}</strong>`;
    pill.onclick = () => SelectDate(pill, dateStr);

    dateContainer.appendChild(pill);

    if (i === 0) selectedDateInput.value = dateStr;
}

function SelectDate(element, dateStr) {
    document.querySelectorAll('.DatePill').forEach(el => el.classList.remove('Selected'));
    element.classList.add('Selected');
    selectedDateInput.value = dateStr;
    FetchAvailability();
}

function SelectSlot(element, slotId) {
    if (element.classList.contains('Disabled')) return;
    document.querySelectorAll('.TimeSlot').forEach(el => el.classList.remove('Selected'));
    element.classList.add('Selected');
    document.getElementById('SelectedSlotId').value = slotId;
}

async function FetchAvailability() {
    const locationId = document.getElementById('LocationId').value;
    const date = document.getElementById('SelectedDate').value;
    const timeContainer = document.getElementById('TimeContainer');

    if (!locationId || !date) return;

    timeContainer.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #888;">Checking availability...</div>';
    document.getElementById('SelectedSlotId').value = ''; // Reset selected slot to force re-selection

    try {
        const response = await fetch(`APIs/GetTourSlots.php?locationId=${locationId}&date=${date}`);
        const slots = await response.json();

        timeContainer.innerHTML = '';

        if (slots.length === 0) {
            timeContainer.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #888;">No slots available.</div>';
            return;
        }

        slots.forEach(slot => {
            const div = document.createElement('div');
            div.className = `TimeSlot ${slot.IsFull ? 'Disabled' : ''}`;
            div.innerText = slot.DisplayTime;
            div.onclick = () => SelectSlot(div, slot.id);
            timeContainer.appendChild(div);
        });

    } catch (error) {
        console.error(error);
        timeContainer.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #red;">Error loading times.</div>';
    }
}

// Form Submission
const tourForm = document.getElementById('TourForm');
if (tourForm) {
    tourForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const btn = document.querySelector('.BtnSubmit');
        const originalText = btn.innerHTML;

        // Basic Client Validation
        const slotId = document.getElementById('SelectedSlotId').value;
        if (!slotId) {
            alert("Please select a time slot.");
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Booking...';

        const formData = {
            FullName: document.getElementById('FullName').value,
            Email: document.getElementById('Email').value,
            Phone: document.getElementById('Phone').value,
            LocationId: document.getElementById('LocationId').value,
            TourDate: document.getElementById('SelectedDate').value,
            SlotId: slotId
        };

        try {
            const response = await fetch('APIs/BookTour.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                // Extract display text for confirmation page
                const locationName = document.getElementById('LocationId').options[document.getElementById('LocationId').selectedIndex].text;
                const slotEl = document.querySelector('.TimeSlot.Selected');
                const slotText = slotEl ? slotEl.innerText : 'Scheduled Time';

                // Redirect to Success Page with params
                const params = new URLSearchParams({
                    date: formData.TourDate,
                    time: slotText,
                    location: locationName
                });

                window.location.href = `TourSuccess.php?${params.toString()}`;
            } else {
                alert(result.error || 'Booking failed.');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        } catch (error) {
            console.error(error);
            alert('An error occurred.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
}
