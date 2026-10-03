<?php
// NammaHub/api/signup.php
// Place at: C:/xampp/htdocs/NammaHub/api/signup.php

require_once __DIR__ . '/config.php';
jsonHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$firstName = trim($input['firstName'] ?? '');
$lastName  = trim($input['lastName']  ?? '');
$email     = strtolower(trim($input['email']    ?? ''));
$password  = $input['password']  ?? '';
$role      = $input['role']      ?? 'user';
$adminKey  = $input['adminKey']  ?? '';

// ── Validation ───────────────────────────────────────────────
if (!$firstName || !$lastName) {
    http_response_code(400);
    echo json_encode(['error' => 'First and last name are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email address.']);
    exit;
}

if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 8 characters.']);
    exit;
}

$role = in_array($role, ['user', 'admin']) ? $role : 'user';

// Admin key check
if ($role === 'admin') {
    if ($adminKey !== ADMIN_SECRET_KEY) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid admin secret key.']);
        exit;
    }
}

// ── DB operations ────────────────────────────────────────────
$pdo = getDB();

// Check duplicate email
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'An account with this email already exists.']);
    exit;
}

// Insert
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
$stmt = $pdo->prepare(
    'INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)'
);
$stmt->execute([$firstName, $lastName, $email, $hash, $role]);
$newId = $pdo->lastInsertId();

http_response_code(201);
echo json_encode([
    'message' => 'Account created successfully.',
    'user' => [
        'id'         => $newId,
        'first_name' => $firstName,
        'last_name'  => $lastName,
        'email'      => $email,
        'role'       => $role,
    ]
]);
