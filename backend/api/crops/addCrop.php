<?php
header("Content-Type: application/json");
include '../../db_connect.php';

// Decode JSON input
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$field_id = $input["field_id"] ?? null;
$crop_id = $input["crop_id"] ?? null;
$planting_date = $input["planting_date"] ?? null;
$expected_harvest = $input["expected_harvest"] ?? null;

if (!$field_id || !$crop_id || !$planting_date || !$expected_harvest) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO field_crops (field_id, crop_id, planting_date, expected_harvest) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $field_id, $crop_id, $planting_date, $expected_harvest);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Database insert failed"]);
    }

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
