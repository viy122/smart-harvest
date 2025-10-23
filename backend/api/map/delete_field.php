<?php
require_once '../../db_connect.php';
$data = json_decode(file_get_contents("php://input"), true);

$field_id = $data['field_id'] ?? null;
if (!$field_id) {
  echo json_encode(["error" => "Missing field_id"]);
  exit;
}

$stmt = $conn->prepare("DELETE FROM fields WHERE field_id=?");
$stmt->bind_param("i", $field_id);
if ($stmt->execute()) {
  echo json_encode(["message" => "Field deleted successfully"]);
} else {
  echo json_encode(["error" => $stmt->error]);
}
