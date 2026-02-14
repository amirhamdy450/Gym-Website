// js/booking.js

const BookingWizard = {
    currentStep: 1,
    state: {
        instructorId: null,
        instructorName: '',
        selectedSlots: [], // Array of { date: 'Y-m-d', time: 'H:i' }
        currentDateView: null, // "2023-10-25"
        uiMode: 'single', // 'single' | 'recurring'
        repeatWeeks: 1, 
        credits: { plan: 0, wallet: 0 }
    },

    init: function() {
        this.cacheDOM();
        this.bindEvents();
        if (window.UserCredits) this.state.credits = window.UserCredits;
        
        // Initial Render
        this.setMode('single'); // Default to single
    },

    cacheDOM: function() {
        this.modalOverlay = document.getElementById('BookingModal');
        this.wizardContainer = document.querySelector('.WizardContainer');
        this.btnClose = document.getElementById('btnCloseModal');

        this.steps = document.querySelectorAll('.StepInfo');
        this.contents = document.querySelectorAll('.StepContent');
        
        // Global Footer Buttons
        this.btnNext = document.getElementById('btnNext');
        this.btnBack = document.getElementById('btnBack');
        this.btnConfirm = document.getElementById('btnConfirm');
        
        this.trainerCards = document.querySelectorAll('.TrainerCard');
        
        // --- NEW PREMIUM UI ELEMENTS ---
        this.segmentOptions = document.querySelectorAll('.SegmentOption');
        this.dateViewContainer = document.getElementById('dateViewContainer');
        this.dateStrip = document.getElementById('dateStrip');
        this.dateScrollWrapper = document.getElementById('dateScrollWrapper');
        this.btnScrollLeft = document.getElementById('btnScrollLeft');
        this.btnScrollRight = document.getElementById('btnScrollRight');

        this.dayGrid = document.getElementById('dayGrid');
        this.consistencyWarning = document.getElementById('consistencyWarning');

        this.timeSlots = document.getElementById('timeSlots');
        this.selectionCart = document.getElementById('selectionCart'); // The new "Chip" container
        
        this.weeksInputContainer = document.getElementById('weeksInputContainer');
        this.inputWeeks = document.getElementById('inputWeeks');
        this.fallbackDateInput = document.getElementById('bookingDate');
    },

    bindEvents: function() {
        if(this.btnNext) this.btnNext.addEventListener('click', () => this.nextStep());
        if(this.btnBack) this.btnBack.addEventListener('click', () => this.prevStep());
        
        // Confirm Booking Binding
        if(this.btnConfirm) {
            this.btnConfirm.replaceWith(this.btnConfirm.cloneNode(true)); // Remove old listeners
            this.btnConfirm = document.getElementById('btnConfirm'); // Re-select
            this.btnConfirm.addEventListener('click', () => this.submitBooking());
        }

        this.btnClose.addEventListener('click', () => this.close());
        this.modalOverlay.addEventListener('click', (e) => {
            if(e.target === this.modalOverlay) this.close();
        });

        this.trainerCards.forEach(card => {
            card.addEventListener('click', (e) => this.selectTrainer(e.currentTarget));
        });

        // --- Segment Control ---
        this.segmentOptions.forEach(opt => {
            opt.addEventListener('click', (e) => {
                const mode = e.target.dataset.mode;
                this.setMode(mode);
            });
        });

        // --- Date Strip Scroll ---
        if (this.btnScrollLeft) {
            this.btnScrollLeft.addEventListener('click', () => {
                this.dateStrip.scrollBy({ left: -200, behavior: 'smooth' });
            });
        }
        if (this.btnScrollRight) {
            this.btnScrollRight.addEventListener('click', () => {
                this.dateStrip.scrollBy({ left: 200, behavior: 'smooth' });
            });
        }

        // --- Time Slots ---
        if (this.timeSlots) {
            this.timeSlots.addEventListener('click', (e) => {
                if (e.target.classList.contains('TimeSlot') && !e.target.classList.contains('disabled')) {
                    this.toggleTime(e.target);
                }
            });
        }

        // --- Selection Cart (Remove Chips) ---
        if (this.selectionCart) {
            this.selectionCart.addEventListener('click', (e) => {
                if(e.target.closest('.RemoveChipBtn')) {
                    const idx = e.target.closest('.RemoveChipBtn').dataset.index;
                    this.removeSlot(parseInt(idx));
                }
            });
        }

        // --- Weeks Input ---
        if (this.inputWeeks) {
            this.inputWeeks.addEventListener('change', (e) => {
                let val = parseInt(e.target.value);
                if(val < 2) val = 2; 
                if(val > 12) val = 12;
                e.target.value = val;
                this.state.repeatWeeks = val;
                this.renderSelectionCart(); // Updates "Every Mon" text or summary
            });
        }
        
        // --- Fallback Input Picker (Hidden) ---
        if (this.fallbackDateInput) {
            this.fallbackDateInput.addEventListener('change', (e) => {
                if(e.target.value) this.selectDate(e.target.value);
            });
        }
    },

    setMode: function(mode) {
        this.state.uiMode = mode;
        this.state.currentDateView = null; // Reset selection focus
        this.state.selectedSlots = []; // Clear cart on mode switch (UX decision: clear context)
        
        // Update Segment UI
        this.segmentOptions.forEach(opt => {
            opt.classList.toggle('active', opt.dataset.mode === mode);
        });

        // Update Visibility
        if (mode === 'single') {
            if(this.dateScrollWrapper) this.dateScrollWrapper.style.display = 'flex';
            this.dayGrid.style.display = 'none';
            this.weeksInputContainer.style.display = 'none';
            this.state.repeatWeeks = 1;
            this.renderDateStrip();
        } else {
            if(this.dateScrollWrapper) this.dateScrollWrapper.style.display = 'none';
            this.dayGrid.style.display = 'flex';
            this.weeksInputContainer.style.display = 'block';
            this.state.repeatWeeks = parseInt(this.inputWeeks.value) || 4;
            this.renderDayGrid();
        }

        this.renderTimeSlots();
        this.renderSelectionCart();
        this.updateUI();
    },

    // --- Render Logics ---

    renderDateStrip: function() {
        this.dateStrip.innerHTML = '';
        const today = new Date();
        
        // Show 14 days by default
        for(let i=0; i<14; i++) {
            const d = new Date(today);
            d.setDate(today.getDate() + i);
            
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            const dateStr = `${yyyy}-${mm}-${dd}`;
            
            const card = document.createElement('div');
            card.className = 'DateCard';
            if (this.state.currentDateView === dateStr) card.classList.add('selected');

            const dayName = i===0 ? 'Today' : (i===1 ? 'Tmrw' : d.toLocaleDateString('en-US', {weekday:'short'}));
            const dayNum = d.getDate();

            card.innerHTML = `<span class="Day">${dayName}</span><span class="Num">${dayNum}</span>`;
            card.addEventListener('click', () => this.selectDate(dateStr));
            
            this.dateStrip.appendChild(card);
        }
        
        // Handle "Out of Range" selection
        if (this.state.currentDateView) {
            const selDate = new Date(this.state.currentDateView);
            const maxDate = new Date(today); maxDate.setDate(today.getDate() + 13);
            const minDate = new Date(today);
            
            // If selected date is NOT in the [Today...Today+13] range
            /* 
               Simple check: 
               Set time to 0 to compare dates accurately 
            */
            selDate.setHours(0,0,0,0);
            maxDate.setHours(0,0,0,0);
            minDate.setHours(0,0,0,0);

            if (selDate > maxDate) {
                 const card = document.createElement('div');
                 card.className = 'DateCard selected'; 
                 // Force visual style to show it's special? 
                 // Just normal selected is fine.
                 
                 const dayName = selDate.toLocaleDateString('en-US', {weekday:'short'});
                 const dayNum = selDate.getDate();
                 const monName = selDate.toLocaleDateString('en-US', {month:'short'});
                 
                 // Display Month instead of "Mon" to give context
                 card.innerHTML = `<span class="Day">${monName}</span><span class="Num">${dayNum}</span>`;
                 card.addEventListener('click', () => this.selectDate(this.state.currentDateView));
                 
                 // Prepend or Append? Append before "More" button is usually better, 
                 // but if it's far future, maybe Append.
                 this.dateStrip.appendChild(card);
                 // Scroll to end?
                 setTimeout(() => this.dateStrip.scrollLeft = this.dateStrip.scrollWidth, 100);
            }
        }
        
        // "More Dates" Card
        const moreCard = document.createElement('div');
        moreCard.className = 'DateCard';
        moreCard.style.minWidth = '50px';
        moreCard.innerHTML = `<span class="Day">More</span><span class="Num"><i class="fa-regular fa-calendar-days" style="font-size:1.2rem;"></i></span>`;
        moreCard.addEventListener('click', () => {
             if(this.fallbackDateInput.showPicker) this.fallbackDateInput.showPicker();
             else this.fallbackDateInput.click();
        });
        
        this.dateStrip.appendChild(moreCard);
    },

    renderDayGrid: function() {
        this.dayGrid.innerHTML = '';
        const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        
        days.forEach((day, index) => {
            const circle = document.createElement('div');
            circle.className = 'DayCircle';
            circle.textContent = day[0]; 
            circle.innerHTML = `<span style="font-size:0.8rem;">${day}</span>`;
            
            const jsDayIdx = (index + 1) % 7; 
            
            if (this.state.currentDateView) {
                const currentDayIdx = new Date(this.state.currentDateView).getDay();
                if (currentDayIdx === jsDayIdx) circle.classList.add('selected');
            }

            circle.addEventListener('click', () => this.selectWeekday(jsDayIdx));
            this.dayGrid.appendChild(circle);
        });
    },

    renderTimeSlots: function() {
        this.timeSlots.innerHTML = '';
        const times = ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '17:00', '18:00', '19:00'];
        
        if (!this.state.currentDateView) {
             const msg = this.state.uiMode === 'recurring' ? 'Select a weekday above.' : 'Select a date above.';
             this.timeSlots.innerHTML = `<p style="color:#666; grid-column:span 5; text-align:center;">${msg}</p>`;
             return;
        }

        times.forEach(time => {
            const btn = document.createElement('div');
            btn.className = 'TimeSlot';
            btn.textContent = time;
            btn.dataset.time = time;
            
            const isSelected = this.state.selectedSlots.some(s => s.date === this.state.currentDateView && s.time === time);
            if (isSelected) btn.classList.add('selected');

            const slotDateTime = new Date(`${this.state.currentDateView}T${time}`);
            if (slotDateTime < new Date()) {
                 btn.classList.add('disabled');
            }
            
            this.timeSlots.appendChild(btn);
        });
    },

    renderSelectionCart: function() {
        if (!this.selectionCart) return;
        this.selectionCart.innerHTML = '';
        
        if (this.state.selectedSlots.length === 0) {
            return; 
        }

        this.state.selectedSlots.sort((a, b) => new Date(`${a.date}T${a.time}`) - new Date(`${b.date}T${b.time}`));

        this.state.selectedSlots.forEach((slot, idx) => {
            const dateObj = new Date(slot.date);
            let label;
            
            if (this.state.uiMode === 'recurring') {
                const dayName = dateObj.toLocaleDateString('en-US', { weekday: 'short' });
                label = `${dayName} @ ${slot.time}`; // "Mon @ 10:00"
            } else {
                const dateStr = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                label = `${dateStr} @ ${slot.time}`; // "Oct 14 @ 10:00"
            }

            const chip = document.createElement('div');
            chip.className = 'SlotChip';
            chip.innerHTML = `
                <span>${label}</span>
                <i class="fa-solid fa-xmark RemoveChipBtn" data-index="${idx}"></i>
            `;
            this.selectionCart.appendChild(chip);
        });
    },

    // --- Actions ---

    selectDate: function(dateStr) {
        this.state.currentDateView = dateStr;
        this.renderDateStrip(); // To update highlights and potentially render out-of-view date
        this.renderTimeSlots();
    },

    selectWeekday: function(jsDayIdx) {
        // Calculate Next Occurrence
        const d = new Date();
        const todayDay = d.getDay();
        const diff = (jsDayIdx + 7 - todayDay) % 7;
        const daysToAdd = diff === 0 ? 0 : diff; // 0 means today
        
        d.setDate(d.getDate() + daysToAdd);
        
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        const dateStr = `${yyyy}-${mm}-${dd}`;
        
        this.state.currentDateView = dateStr;
        this.renderDayGrid(); 
        this.renderTimeSlots();
    },

    toggleTime: function(slotBtn) {
        if (!this.state.currentDateView) return;
        const time = slotBtn.dataset.time;
        const date = this.state.currentDateView;
        
        const existingIdx = this.state.selectedSlots.findIndex(s => s.date === date && s.time === time);
        
        if (existingIdx >= 0) {
            this.state.selectedSlots.splice(existingIdx, 1); // Remove
        } else {
            // Day swap logic
            const daySlotIdx = this.state.selectedSlots.findIndex(s => s.date === date);
            if (daySlotIdx >= 0) {
                this.state.selectedSlots[daySlotIdx] = { date, time };
            } else {
                this.state.selectedSlots.push({ date, time });
            }
        }
        
        this.renderTimeSlots();
        this.renderSelectionCart();
        this.updateUI(); 
    },

    removeSlot: function(index) {
        this.state.selectedSlots.splice(index, 1);
        this.renderTimeSlots();
        this.renderSelectionCart();
        this.updateUI();
    },

    // --- Checker ---
    
    checkConsistency: function() {
        if (!this.consistencyWarning) return;
        
        if (this.state.uiMode !== 'single' || this.state.selectedSlots.length < 2) {
            this.consistencyWarning.style.display = 'none';
            return;
        }

        // Sort slots by date
        const sorted = [...this.state.selectedSlots].sort((a,b) => new Date(a.date) - new Date(b.date));
        
        let hasLargeGap = false;
        for (let i = 0; i < sorted.length - 1; i++) {
            const d1 = new Date(sorted[i].date);
            const d2 = new Date(sorted[i+1].date);
            
            // Diff in days
            const diffTime = Math.abs(d2 - d1);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
            
            if (diffDays > 14) {
                hasLargeGap = true;
                break;
            }
        }

        this.consistencyWarning.style.display = hasLargeGap ? 'flex' : 'none';
    },

    // --- Standard Methods ---

    open: function() {
        this.modalOverlay.classList.add('open');
        this.reset();
        this.updateUI();
    },

    close: function() {
        this.modalOverlay.classList.remove('open');
    },

    reset: function() {
        this.currentStep = 1;
        this.state.instructorId = null;
        this.state.selectedSlots = [];
        this.state.currentDateView = null;
        this.setMode('single'); // Reset to single
        this.trainerCards.forEach(c => c.classList.remove('selected'));
    },

    updateUI: function() {
        // Steps Indication
        this.steps.forEach((step, index) => {
            const stepNum = index + 1;
            step.classList.toggle('active', stepNum === this.currentStep);
            step.classList.toggle('completed', stepNum < this.currentStep);
        });

        // Content Visibility
        this.contents.forEach((content, index) => {
            content.classList.toggle('active', (index + 1) === this.currentStep);
        });

        // Footer Buttons
        this.btnBack.style.display = this.currentStep === 1 ? 'none' : 'block';
        this.btnNext.style.display = this.currentStep === 3 ? 'none' : 'block';
        this.btnConfirm.style.display = this.currentStep === 3 ? 'block' : 'none';

        // Validation
        let isValid = false;
        if (this.currentStep === 1) isValid = !!this.state.instructorId;
        if (this.currentStep === 2) isValid = this.state.selectedSlots.length > 0;
        
        this.btnNext.disabled = !isValid;
        
        if (this.currentStep === 2) this.checkConsistency();
        if (this.currentStep === 3) this.renderSummary();
    },

    nextStep: function() {
        if (this.currentStep < 3) {
            this.currentStep++;
            this.updateUI();
        }
    },

    prevStep: function() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.updateUI();
        }
    },

    selectTrainer: function(card) {
        this.trainerCards.forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        this.state.instructorId = card.dataset.id;
        this.state.instructorName = card.dataset.name;
        this.updateUI();
    },

    renderSummary: function() {
        document.getElementById('summaryTrainer').textContent = this.state.instructorName;
        
        const count = this.state.selectedSlots.length;
        const weeks = this.state.repeatWeeks;
        const totalSessions = count * weeks;
        
        // Format Date Summary
        let summaryHtml = '';
        this.state.selectedSlots.forEach(s => {
            const d = new Date(s.date);
            const time = s.time;
            if (this.state.uiMode === 'recurring') {
                summaryHtml += `<div>${d.toLocaleDateString('en-US', {weekday:'long'})} @ ${time} <small style='color:#888'>(x${weeks} wks)</small></div>`;
            } else {
                 summaryHtml += `<div>${d.toLocaleDateString('en-US', {month:'short', day:'numeric'})} @ ${time}</div>`;
            }
        });
        
        document.getElementById('summaryDate').innerHTML = summaryHtml || '-'; 
        document.getElementById('summaryTime').innerHTML = `<span class="Highlight">${totalSessions} Total Sessions</span>`;
        document.getElementById('summaryRepeat').textContent = this.state.uiMode === 'recurring' ? `${weeks} Weeks` : 'One-time';
        
        // Credits Logic
        const totalCost = totalSessions; // 1 credit per session
        const credits = this.state.credits;
        const balance = parseInt(credits.plan) + parseInt(credits.wallet);
        
        const balanceEl = document.getElementById('creditBalance');
        if (balance >= totalCost) {
            balanceEl.innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span>Total Cost: <span style="color:var(--neon-red); font-size:1.4rem; font-weight:900;">${totalCost}</span> Credits</span>
                </div>
                <div style="color:#888; font-size:0.9rem; margin-top:5px; border-top:1px solid #444; padding-top:5px;">
                    Balance: ${balance} &rarr; <span style="color:#fff;">${balance - totalCost} remaining</span>
                </div>
            `;
            this.btnConfirm.disabled = false;
        } else {
            balanceEl.innerHTML = `<span style='color:red; font-weight:bold;'>Insufficient Credits</span> <br><small>Need ${totalCost}, Have ${balance}</small>`;
            this.btnConfirm.disabled = true;
        }
    },

    submitBooking: function() {
        const btn = this.btnConfirm;
        if(btn.disabled) return;
        
        console.log('Submitting Booking:', this.state);
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Booking...';

        fetch('../../APIs/BookSession.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                InstructorId: this.state.instructorId,
                Sessions: this.state.selectedSlots,
                RepeatWeeks: this.state.repeatWeeks
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload(); 
            } else {
                alert('Error: ' + data.error);
                btn.disabled = false;
                btn.textContent = 'Confirm Booking';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network Error');
            btn.disabled = false;
            btn.textContent = 'Confirm Booking';
        });
    }
};

// Session Details Modal Logic
const SessionModal = {
    modal: document.getElementById('SessionDetailsModal'),
    data: null,

    open: function(sessionData) {
        this.data = sessionData;
        console.log("Opening Session:", sessionData);
        
        // Ensure Modal Exists (might be null if script loaded before DOM)
        this.modal = document.getElementById('SessionDetailsModal');
        if(!this.modal) return;

        // Populate Fields
        const typeEl = document.getElementById('modalSessionType');
        if(typeEl) typeEl.textContent = (sessionData.Category || 'Private Session').toUpperCase();
        
        const titleEl = document.getElementById('modalSessionTitle');
        if(titleEl) titleEl.textContent = sessionData.Category || 'Workout';
        
        // Date & Time
        const dateObj = new Date(sessionData.StartTime);
        const timeStr = dateObj.toLocaleTimeString('en-US', {hour:'2-digit', minute:'2-digit'});
        const dateStr = dateObj.toLocaleDateString('en-US', {weekday:'short', month:'short', day:'numeric'});
        
        // Calculate End Time
        const duration = parseInt(sessionData.DurationMinutes) || 60;
        const endDate = new Date(dateObj.getTime() + duration * 60000);
        const endTimeStr = endDate.toLocaleTimeString('en-US', {hour:'2-digit', minute:'2-digit'});

        const timeEl = document.getElementById('modalSessionTime');
        if(timeEl) timeEl.textContent = `${timeStr} - ${endTimeStr}`;
        
        const dateEl = document.getElementById('modalSessionDate');
        if(dateEl) dateEl.textContent = dateStr;

        // Instructor
        const instrEl = document.getElementById('modalInstructorName');
        if(instrEl) instrEl.textContent = sessionData.InstructorName || 'TBD';
        
        // Location
        const locEl = document.getElementById('modalLocation');
        if(locEl) locEl.innerHTML = `<i class="fa-solid fa-location-dot"></i> ${sessionData.Location || 'Main Gym'}`;

        // Notes (if any)
        const notesEl = document.getElementById('modalNotes');
        if(notesEl) {
            if (sessionData.Notes) {
                notesEl.textContent = sessionData.Notes;
                notesEl.style.display = 'block';
            } else {
                notesEl.style.display = 'none';
            }
        }

        // Show Modal
        this.modal.style.display = 'flex';
        // Small delay for transition
        setTimeout(() => this.modal.classList.add('active'), 10);
    },

    close: function() {
        this.modal = document.getElementById('SessionDetailsModal');
        if(this.modal) {
            this.modal.classList.remove('active');
            setTimeout(() => this.modal.style.display = 'none', 300);
        }
    },

    cancelSession: function() {
        if(!confirm('Are you sure you want to cancel this session?')) return;
        
        if(!this.data || !this.data.id) return;

        // Call API
        fetch('../../APIs/SessionActions.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                action: 'cancel',
                sessionId: this.data.id,
                type: this.data.Type // 'Private' or 'Program'
            })
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                alert('Session cancelled successfully.');
                window.location.reload();
            } else {
                alert('Error: ' + res.error);
            }
        })
        .catch(err => alert('Network error'));
    }
};

// Close modal on click outside
document.addEventListener('click', (e) => {
    const modal = document.getElementById('SessionDetailsModal');
    if (modal && e.target === modal) {
        SessionModal.close();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    BookingWizard.init();
});
