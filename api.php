<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require 'config/db.php'; // adjust path if api.php is in a different folder

$result = mysqli_query($conn, 'SELECT * FROM restaurants ORDER BY featured DESC, id ASC');

if (!$result) {
    echo json_encode(["error" => mysqli_error($conn)]);
    exit();
}

$rows = [];
while ($r = mysqli_fetch_assoc($result)) {
    $r['images']   = array_values(array_filter(explode(',', $r['images'] ?? '')));
    $r['is_veg']   = (bool)$r['is_veg'];
    $r['alcohol']  = (bool)$r['alcohol'];
    $r['open']     = (bool)$r['open'];
    $r['featured'] = (bool)$r['featured'];
    $r['price']    = (int)$r['price'];
    $r['rating']   = (float)$r['rating'];
    $r['reviews']  = (int)$r['reviews'];
    $rows[] = $r;
}

echo json_encode($rows);