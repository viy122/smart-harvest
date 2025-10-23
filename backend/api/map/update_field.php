<?php
require_once '../../db_connect.php';
$data = json_decode(file_get_contents("php://input"), true);

$field_id = $data['field_id'] ?? null;
$geometry = $data['geometry'] ?? null;

if (!$field_id || !$geometry) {
  echo json_encode(["error" => "Missing data"]);
  exit;
}

$stmt = $conn->prepare("UPDATE fields SET geometry=? WHERE field_id=?");
$stmt->bind_param("si", $geometry, $field_id);
if ($stmt->execute()) {
  echo json_encode(["message" => "Field updated successfully"]);
} else {
  echo json_encode(["error" => $stmt->error]);
}
