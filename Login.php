<?php
// Root Entry Point: Login.php
require_once 'Includes/DB.php';
require_once 'Includes/Auth.php';

// Redirect if already logged in
if (AuthIsLoggedIn()) {
    header("Location: index.php"); // Or dashboard
    exit;
}

$Error = '';

// Handle Login Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Email = trim($_POST['email'] ?? '');
    $Password = $_POST['password'] ?? '';

    if (!$Email || !$Password) {
        $Error = "Please enter both email and password.";
    } else {
        // Fetch User
        $Stmt = $pdo->prepare("SELECT * FROM Users WHERE Email = ? LIMIT 1");
        $Stmt->execute([$Email]);
        $User = $Stmt->fetch(PDO::FETCH_ASSOC);

        if ($User && password_verify($Password, $User['PasswordHash'])) {
            AuthLogin($User);

            // Redirect based on role
            if ($User['Role'] === 'Trainee') {
                header("Location: Pages/Trainee/Dashboard.php");
            } elseif ($User['Role'] === 'Admin') {
                header("Location: Pages/Admin/Dashboard.php");
            } elseif ($User['Role'] === 'Instructor') {
                header("Location: Pages/Instructor/Dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $Error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Epix Gym</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/signup.css">
</head>

<body class="SignupBody LoginPage">

    <?php
    $HideNavLinks = true;
    include 'Includes/NavBar.php';
    ?>

    <div class="LoginWrapper">
        <div class="LoginContainer">

            <h2>Welcome Back</h2>
            <p class="SubText">Access your performance dashboard</p>

            <?php if ($Error): ?>
                <div class="ErrorMessage LoginErrorMessage">
                    <?= htmlspecialchars($Error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="Login.php">

                <div class="FormGroup">
                    <label for="Email">EMAIL ADDRESS</label>
                    <input type="email" name="email" id="Email" class="InputBox" placeholder="athlete@epixgym.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="FormGroup" style="position: relative;">
                    <div style="display: flex; justify-content: space-between;">
                        <label for="Password">PASSWORD</label>
                        <a href="#" class="ForgotLink">Forgot password?</a>
                    </div>
                    <input type="password" name="password" id="Password" class="InputBox" placeholder="••••••••" required>
                    <span class="EyeIcon">👁️</span> <!-- Event listener attached in external JS -->
                </div>

                <div class="KeepSigned">
                    <label class="KeepSignedLabel">
                        <input type="checkbox" name="remember" class="KeepSignedCheckbox">
                        Keep me signed in
                    </label>
                </div>

                <button type="submit" class="BtnPrimary">SIGN IN</button>

                <div class="Divider">
                    <span>OR</span>
                </div>

                <button type="button" class="BtnGoogle">
                    <span style="margin-right: 0.5rem;">G</span> Continue with Google
                </button>

            </form>

        </div>

        <div class="BottomLink">
            New to the tribe? <a href="Signup.php">Start your 3-step sign up</a>
        </div>
    </div>

    <div class="FooterCopyright">
        &copy; 2024 EPIX GYM PERFORMANCE. ENGINEERED FOR GREATNESS.
    </div>

    <script src="js/login.js"></script>
</body>

</html>