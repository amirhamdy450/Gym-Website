document.addEventListener('DOMContentLoaded', () => {
    const EyeIcon = document.querySelector('.EyeIcon');
    if (EyeIcon) {
        EyeIcon.addEventListener('click', TogglePassword);
    }
});

function TogglePassword() {
    const PwdInput = document.getElementById('Password');
    if (PwdInput.type === 'password') {
        PwdInput.type = 'text';
    } else {
        PwdInput.type = 'password';
    }
}
