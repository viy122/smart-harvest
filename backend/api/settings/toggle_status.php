<?php

header('Content-Type: application/json');
include '../../db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);

$id = intval($input['id'] ?? 0);
$role = trim($input['role'] ?? '');
$status = trim($input['status'] ?? '');

if ($id <= 0 || empty($role) || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// Only admins can have status toggled
if ($role !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Only admin status can be toggled']);
    exit;
}

try {
    $isActive = ($status === 'active') ? 1 : 0;

    $stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE user_id = ? AND role = 1");
    $stmt->bind_param("ii", $isActive, $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>