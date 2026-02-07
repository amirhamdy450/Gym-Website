<?php
// Includes/Auth.php
session_start();

// Check if user is logged in
function AuthIsLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Require user to be logged in
function AuthRequireLogin() {
    if (!AuthIsLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}

// Require specific role
function AuthRequireRole($Role) {
    AuthRequireLogin();
    if ($_SESSION['user_role'] !== $Role) {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }
}

// Login user
function AuthLogin($User) {
    $_SESSION['user_id'] = $User['id'];
    $_SESSION['user_name'] = $User['FirstName'] . ' ' . $User['LastName'];
    $_SESSION['user_role'] = $User['Role'];
    $_SESSION['user_email'] = $User['Email'];
}

// Logout
function AuthLogout() {
    session_unset();
    session_destroy();
}

// Get current user ID
function AuthId() {
    return $_SESSION['user_id'] ?? null;
}
?>
