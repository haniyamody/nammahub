<?php
// NammaHub/api/login.php
// Place at: C:/xampp/htdocs/NammaHub/api/login.php

require_once __DIR__ . '/config.php';
jsonHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

$input    = json_decode(file_get_contents('php://input'), true);
$email    = strtolower(trim($input['email']    ?? ''));
$password = $input['password'] ?? '';
$mode     = $input['mode']     ?? 'user'; // 'admin' or 'user'

// ── Validation ───────────────────────────────────────────────
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email address.']);
    exit;
}

if (!$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Password is required.']);
    exit;
}

// ── DB lookup ────────────────────────────────────────────────
$pdo  = getDB();
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Incorrect email or password.']);
    exit;
}

// Role mismatch check
if ($mode === 'admin' && $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'This account does not have admin privileges.']);
    exit;
}

if ($mode === 'user' && $user['role'] === 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Please use the Admin tab to sign in.']);
    exit;
}

// Re-hash if needed (security best practice)
if (password_needs_rehash($user['password'], PASSWORD_BCRYPT, ['cost' => 12])) {
    $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([$newHash, $user['id']]);
}

http_response_code(200);
echo json_encode([
    'message' => 'Login successful.',
    'token' => (string)$user['id'],
    'user' => [
        'id'         => $user['id'],
        'first_name' => $user['first_name'],
        'last_name'  => $user['last_name'],
        'email'      => $user['email'],
        'role'       => $user['role'],
    ]
]);
