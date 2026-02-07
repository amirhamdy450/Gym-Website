<?php
// Includes/Validation.php

// Sanitize string input
function ValSanitize($Input) {
    return htmlspecialchars(strip_tags(trim($Input)));
}

// Validate email
function ValEmail($Email) {
    return filter_var($Email, FILTER_VALIDATE_EMAIL);
}

// Validate password (min 6 chars)
function ValPassword($Password) {
    return strlen($Password) >= 6;
}

// Check required fields
function ValRequired($Data, $Fields) {
    $Missing = [];
    foreach ($Fields as $Field) {
        if (!isset($Data[$Field]) || empty(trim($Data[$Field]))) {
            $Missing[] = $Field;
        }
    }
    return $Missing;
}
?>
