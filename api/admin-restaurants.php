<?php
require_once __DIR__ . '/config.php';
jsonHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

// Read body FIRST
$input = json_decode(file_get_contents('php://input'), true);

// Auth check using body
$userId = (int)($input['auth_user_id'] ?? 0);
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized.']);
    exit;
}

$pdo  = getDB();
$stmt = $pdo->prepare('SELECT role FROM users WHERE id = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch();
if (!$user || $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Admin access required.']);
    exit;
}

$action = $input['action'] ?? '';

// ── ADD ──────────────────────────────────────────────────────
if ($action === 'add') {
    $required = ['name','cuisine','area','tagline','price','rating'];
    foreach ($required as $f) {
        if (empty($input[$f])) {
            http_response_code(400);
            echo json_encode(['error' => "Field '$f' is required."]);
            exit;
        }
    }
    $stmt = $pdo->prepare("
        INSERT INTO restaurants
          (name, cuisine, area, tagline, price, rating, reviews, is_veg, alcohol, timing, open, featured, image1, image2, image3, ribbon)
        VALUES
          (:name,:cuisine,:area,:tagline,:price,:rating,:reviews,:is_veg,:alcohol,:timing,:open,:featured,:image1,:image2,:image3,:ribbon)
    ");
    $stmt->execute([
        ':name'     => trim($input['name']),
        ':cuisine'  => trim($input['cuisine']),
        ':area'     => trim($input['area']),
        ':tagline'  => trim($input['tagline']),
        ':price'    => (int)$input['price'],
        ':rating'   => (float)$input['rating'],
        ':reviews'  => (int)($input['reviews'] ?? 0),
        ':is_veg'   => (int)!empty($input['is_veg']),
        ':alcohol'  => (int)!empty($input['alcohol']),
        ':timing'   => trim($input['timing'] ?? ''),
        ':open'     => (int)!empty($input['open']),
        ':featured' => (int)!empty($input['featured']),
        ':image1'   => trim($input['image1'] ?? ''),
        ':image2'   => trim($input['image2'] ?? ''),
        ':image3'   => trim($input['image3'] ?? ''),
        ':ribbon'   => trim($input['ribbon'] ?? ''),
    ]);
    echo json_encode(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
    exit;
}

// ── UPDATE ────────────────────────────────────────────────────
if ($action === 'update') {
    $id = (int)($input['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID required.']); exit; }
    $stmt = $pdo->prepare("
        UPDATE restaurants SET
          name=:name, cuisine=:cuisine, area=:area, tagline=:tagline,
          price=:price, rating=:rating, reviews=:reviews, is_veg=:is_veg,
          alcohol=:alcohol, timing=:timing, open=:open, featured=:featured,
          image1=:image1, image2=:image2, image3=:image3, ribbon=:ribbon
        WHERE id=:id
    ");
    $stmt->execute([
        ':id'       => $id,
        ':name'     => trim($input['name']),
        ':cuisine'  => trim($input['cuisine']),
        ':area'     => trim($input['area']),
        ':tagline'  => trim($input['tagline']),
        ':price'    => (int)$input['price'],
        ':rating'   => (float)$input['rating'],
        ':reviews'  => (int)($input['reviews'] ?? 0),
        ':is_veg'   => (int)!empty($input['is_veg']),
        ':alcohol'  => (int)!empty($input['alcohol']),
        ':timing'   => trim($input['timing'] ?? ''),
        ':open'     => (int)!empty($input['open']),
        ':featured' => (int)!empty($input['featured']),
        ':image1'   => trim($input['image1'] ?? ''),
        ':image2'   => trim($input['image2'] ?? ''),
        ':image3'   => trim($input['image3'] ?? ''),
        ':ribbon'   => trim($input['ribbon'] ?? ''),
    ]);
    echo json_encode(['success' => true]);
    exit;
}

// ── DELETE ────────────────────────────────────────────────────
if ($action === 'delete') {
    $id = (int)($input['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['error' => 'ID required.']); exit; }
    $pdo->prepare('DELETE FROM restaurants WHERE id = ?')->execute([$id]);
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action.']);
