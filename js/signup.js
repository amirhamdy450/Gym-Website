// js/signup.js

// State Management
const SignupState = {
    // Identity
    FirstName: '',
    LastName: '',
    Email: '',
    DateOfBirth: '',
    Gender: '',
    Password: '',

    // Physical
    Height: '',
    Weight: '',
    FitnessGoals: [], // Array of strings

    // Membership
    MembershipId: null
};

// --- Initialization ---

document.addEventListener('DOMContentLoaded', () => {
    // Step 1 Events
    document.getElementById('BtnStep1Next')?.addEventListener('click', ValidateStep1);

    document.querySelectorAll('input[name="Gender"]').forEach(radio => {
        radio.addEventListener('change', (e) => HandleGenderSelect(e.target.value));
    });

    // Step 2 Events
    document.getElementById('BtnStep2Next')?.addEventListener('click', ValidateStep2);
    document.getElementById('BtnStep2Back')?.addEventListener('click', () => ShowStep('Step1'));

    // Goal Toggles
    document.querySelectorAll('.GoalOption').forEach(btn => {
        btn.addEventListener('click', () => HandleGoalToggle(btn));
    });

    // Step 3 Events
    document.querySelectorAll('.SelectablePlan').forEach(card => {
        card.addEventListener('click', () => SelectMembership(card));
    });

    document.getElementById('BtnStep3Back')?.addEventListener('click', () => ShowStep('Step2'));
    document.getElementById('FinalSubmitBtn')?.addEventListener('click', SubmitSignup);
});


// --- Step 1 Logic ---

function HandleGenderSelect(gender) {
    document.querySelectorAll('.GenderOption').forEach(el => el.classList.remove('Selected'));
    const label = document.getElementById(`Label${gender}`);
    if (label) label.classList.add('Selected');
    SignupState.Gender = gender;
}

function ValidateStep1() {
    let isValid = true; // Track validity to show all errors at once

    // inputs
    const InputFN = document.getElementById('FirstName');
    const InputLN = document.getElementById('LastName');
    const InputEmail = document.getElementById('Email');
    const InputDOB = document.getElementById('DateOfBirth');
    const InputPwd = document.getElementById('Password');
    const InputCfm = document.getElementById('ConfirmPassword');

    // Values
    const fn = InputFN.value.trim();
    const ln = InputLN.value.trim();
    const em = InputEmail.value.trim();
    const dobValue = InputDOB.value;
    const pwd = InputPwd.value;
    const cfm = InputCfm.value;

    // Clear previous errors
    [InputFN, InputLN, InputEmail, InputDOB, InputPwd, InputCfm].forEach(ClearError);
    document.getElementById('LabelMale').classList.remove('InputError'); // Custom for gender logic if needed

    // 1. Name Validation
    if (!fn) { SetError(InputFN, "First Name is required."); isValid = false; }
    if (!ln) { SetError(InputLN, "Last Name is required."); isValid = false; }

    // 2. Email Validation
    if (!em || !em.includes('@') || !em.includes('.')) {
        SetError(InputEmail, "Please enter a valid email.");
        isValid = false;
    }

    // 3. Date of Birth & Age Validation
    if (!dobValue) {
        SetError(InputDOB, "Date of Birth is required.");
        isValid = false;
    } else {
        const dobDate = new Date(dobValue);
        const today = new Date();
        const minAgeDate = new Date(today.getFullYear() - 15, today.getMonth(), today.getDate());
        const maxAgeDate = new Date(today.getFullYear() - 100, today.getMonth(), today.getDate());

        if (dobDate > today) {
            SetError(InputDOB, "Date cannot be in the future.");
            isValid = false;
        } else if (dobDate > minAgeDate) {
            SetError(InputDOB, "You must be at least 15 years old to join.");
            isValid = false;
        } else if (dobDate < maxAgeDate) {
            SetError(InputDOB, "Please enter a valid date.");
            isValid = false;
        }
    }

    // 4. Gender Validation
    if (!SignupState.Gender) {
        // No input box for gender, so we might use a generic alert or style the labels
        // For now, let's just alert used as a fallback or style the container
        // Simple fallback:
        // AlertError("Please select your gender.");
        // Better:
        const genderGrid = document.querySelector('.GenderGrid');
        if (!document.querySelector('#GenderError')) {
            const err = document.createElement('span');
            err.className = 'ErrorMessage';
            err.id = 'GenderError';
            err.innerText = "Please select a gender.";
            genderGrid.parentNode.appendChild(err);
        }
        isValid = false;
    } else {
        const err = document.querySelector('#GenderError');
        if (err) err.remove();
    }

    // 5. Password Validation
    const strongPwdRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/;
    if (!pwd) {
        SetError(InputPwd, "Password is required.");
        isValid = false;
    } else if (!strongPwdRegex.test(pwd)) {
        SetError(InputPwd, "Password must be 8+ chars, with 1 Upper, 1 Lower, 1 Number, and 1 Symbol (!@#$%^&*).");
        isValid = false;
    }

    if (pwd !== cfm) {
        SetError(InputCfm, "Passwords do not match.");
        isValid = false;
    }

    if (!isValid) return; // Stop if any errors

    // Update State
    SignupState.FirstName = fn;
    SignupState.LastName = ln;
    SignupState.Email = em;
    SignupState.DateOfBirth = dobValue;
    SignupState.Password = pwd;

    ShowStep('Step2');
}

// --- Validation Helpers ---
function SetError(input, msg) {
    input.classList.add('InputError');
    // Check if error message already exists
    let errorDisplay = input.parentNode.querySelector('.ErrorMessage');
    if (!errorDisplay) {
        errorDisplay = document.createElement('span');
        errorDisplay.className = 'ErrorMessage';
        input.parentNode.appendChild(errorDisplay);
    }
    errorDisplay.textContent = msg;
}

function ClearError(input) {
    input.classList.remove('InputError');
    const errorDisplay = input.parentNode.querySelector('.ErrorMessage');
    if (errorDisplay) {
        errorDisplay.remove();
    }
}

// --- Step 2 Logic ---

function HandleGoalToggle(btn) {
    const goal = btn.dataset.goal;

    // Check if exists
    if (SignupState.FitnessGoals.includes(goal)) {
        // Remove
        SignupState.FitnessGoals = SignupState.FitnessGoals.filter(g => g !== goal);
        btn.classList.remove('Selected');
    } else {
        // Add
        SignupState.FitnessGoals.push(goal);
        btn.classList.add('Selected');
    }
}

function ValidateStep2() {
    const h = document.getElementById('Height').value;
    const w = document.getElementById('Weight').value;

    if (!h || h < 100 || h > 250) return AlertError("Please enter a valid height (cm).");
    if (!w || w < 30 || w > 300) return AlertError("Please enter a valid weight (kg).");
    if (SignupState.FitnessGoals.length === 0) return AlertError("Please select at least one fitness goal.");

    SignupState.Height = h;
    SignupState.Weight = w;

    ShowStep('Step3');
}

// --- Step 3 Logic ---
function SelectMembership(card) {
    const id = card.dataset.id;
    SignupState.MembershipId = id;

    // UI: Remove old selection, add new
    document.querySelectorAll('.SelectablePlan').forEach(c => {
        c.classList.remove('SelectedPlan');
        c.querySelector('.FakeButton').textContent = "SELECT PLAN";
    });

    card.classList.add('SelectedPlan');
    card.querySelector('.FakeButton').textContent = "SELECTED";

    // Enable Submit Button
    const submitBtn = document.getElementById('FinalSubmitBtn');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.classList.add('BtnPrimary');
        submitBtn.classList.remove('BtnDisabled');
    }
}

// --- Submission ---
async function SubmitSignup() {
    if (!SignupState.MembershipId) return AlertError("Please select a membership plan.");

    const btn = document.getElementById('FinalSubmitBtn');
    if (btn) {
        btn.innerHTML = 'Creating Account...';
        btn.disabled = true;
    }
    document.body.style.cursor = 'wait';

    try {
        const response = await fetch('APIs/Signup.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(SignupState)
        });

        const result = await response.json();

        if (response.ok) {
            window.location.href = 'index.php?welcome=1';
        } else {
            document.body.style.cursor = 'default';
            alert("Signup Failed: " + (result.error || "Unknown error"));
            if (btn) {
                btn.innerHTML = 'COMPLETE SIGNUP';
                btn.disabled = false;
            }
        }
    } catch (e) {
        console.error(e);
        document.body.style.cursor = 'default';
        alert("Network error. Please try again.");
        if (btn) {
            btn.innerHTML = 'COMPLETE SIGNUP';
            btn.disabled = false;
        }
    }
}


// --- Utilities ---
function ShowStep(stepId) {
    document.querySelectorAll('.StepGroup').forEach(grp => grp.classList.remove('Active'));
    document.getElementById(stepId).classList.add('Active');

    // Dynamic Width for Step 3
    const container = document.querySelector('.WizardContainer');
    if (stepId === 'Step3') {
        container.classList.add('Wide');
    } else {
        container.classList.remove('Wide');
    }
}

function AlertError(msg) {
    alert(msg);
    return false;
}
