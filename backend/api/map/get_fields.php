<?php
header("Content-Type: application/json");

$host = "localhost";
$user = "root";
$pass = "";
$db   = "smart_harvest"; // ✅ use your DB

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

$sql = "SELECT field_id, name, area, perimeter, type, notes, geometry FROM fields";
$result = $conn->query($sql);

$fields = [];
while ($row = $result->fetch_assoc()) {
    $row['geometry'] = json_decode($row['geometry']);
    $fields[] = $row;
}

echo json_encode($fields);
$conn->close();
?>
