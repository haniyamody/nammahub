<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$conn = new mysqli('localhost', 'root', '', 'namma_blr');
if ($conn->connect_error) {
    echo json_encode(['error' => 'DB connection failed']); exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// ── GET: fetch all events ──────────────────────────────────────
if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM events ORDER BY id ASC");
    $events = [];
    while ($row = $result->fetch_assoc()) $events[] = $row;
    echo json_encode($events);
    exit;
}

// ── POST: add or update ───────────────────────────────────────
if ($method === 'POST') {
    $data      = json_decode(file_get_contents('php://input'), true);
    $id        = (int)($data['id']        ?? 0);
    $day       = $conn->real_escape_string($data['day']       ?? '');
    $month     = $conn->real_escape_string($data['month']     ?? '');
    $category  = $conn->real_escape_string($data['category']  ?? '');
    $name      = $conn->real_escape_string($data['name']      ?? '');
    $location  = $conn->real_escape_string($data['location']  ?? '');
    $time      = $conn->real_escape_string($data['time']      ?? '');
    $duration  = $conn->real_escape_string($data['duration']  ?? '');
    $price     = $conn->real_escape_string($data['price']     ?? '');
    $price_sub = $conn->real_escape_string($data['price_sub'] ?? '');
    $btn_label = $conn->real_escape_string($data['btn_label'] ?? 'Book Tickets');

    if ($id > 0) {
        $ok = $conn->query("UPDATE events SET
            day='$day', month='$month', category='$category', name='$name',
            location='$location', time='$time', duration='$duration',
            price='$price', price_sub='$price_sub', btn_label='$btn_label'
            WHERE id=$id");
        echo json_encode(['success' => (bool)$ok, 'action' => 'updated', 'id' => $id]);
    } else {
        $ok = $conn->query("INSERT INTO events
            (day,month,category,name,location,time,duration,price,price_sub,btn_label)
            VALUES ('$day','$month','$category','$name','$location','$time','$duration','$price','$price_sub','$btn_label')");
        echo json_encode(['success' => (bool)$ok, 'action' => 'inserted', 'id' => $conn->insert_id]);
    }
    exit;
}

// ── DELETE ────────────────────────────────────────────────────
if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id   = (int)($data['id'] ?? 0);
    if ($id > 0) {
        $ok = $conn->query("DELETE FROM events WHERE id=$id");
        echo json_encode(['success' => (bool)$ok]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid ID']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);

