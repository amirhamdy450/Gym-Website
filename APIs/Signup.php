<?php
// APIs/Signup.php
require_once '../Includes/DB.php';
require_once '../Includes/Auth.php';

header('Content-Type: application/json');

// Check method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get JSON Input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

// Extract & Sanitize
$firstName = trim($input['FirstName'] ?? '');
$lastName = trim($input['LastName'] ?? '');
$email = trim($input['Email'] ?? '');
$password = $input['Password'] ?? '';
$gender = $input['Gender'] ?? null;
$dob = $input['DateOfBirth'] ?? null;
$height = $input['Height'] ?? null; // Optional
$weight = $input['Weight'] ?? null; // Optional
$goals = $input['FitnessGoals'] ?? []; // Array
$membershipId = $input['MembershipId'] ?? null; // Optional for now

// Basic Backend Validation
if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields (Name, Email, Password)']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

// Check if Email Exists
try {
    $stmt = $pdo->prepare("SELECT id FROM Users WHERE Email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409); // Conflict
        echo json_encode(['error' => 'Email already registered']);
        exit;
    }

    // Hash Password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Prepare JSON for Goals
    $goalsJson = !empty($goals) ? json_encode($goals) : null;

    // Insert into Users (Single Table)
    // Note: Role defaults to 'Trainee' via DB schema default
    $query = "INSERT INTO Users 
              (FirstName, LastName, Email, PasswordHash, Gender, DateOfBirth, Height, Weight, FitnessGoals) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $insert = $pdo->prepare($query);
    $insert->execute([
        $firstName,
        $lastName,
        $email,
        $passwordHash,
        $gender,
        $dob,
        $height,
        $weight,
        $goalsJson
    ]);

    $newUserId = $pdo->lastInsertId();

    // Start Session (Auto-Login)
    $_SESSION['user_id'] = $newUserId;
    $_SESSION['user_role'] = 'Trainee'; // Default
    $_SESSION['user_name'] = $firstName . ' ' . $lastName;

    // Success
    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully',
        'userId' => $newUserId
    ]);

} catch (PDOException $e) {
    // Log error in production, show simple message here
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
