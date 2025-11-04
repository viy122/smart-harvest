<?php

header('Content-Type: application/json');
include '../../db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);

$id = intval($input['id'] ?? 0);
$role = trim($input['role'] ?? '');

if ($id <= 0 || empty($role)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    if ($role === 'Admin') {
        // Delete from users table
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 1");
        $stmt->bind_param("i", $id);

    } elseif ($role === 'Farmer') {
        // Delete from farmers table
        $stmt = $conn->prepare("DELETE FROM farmers WHERE farmer_id = ?");
        $stmt->bind_param("i", $id);

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid role']);
        exit;
    }

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Person deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete person or person not found']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>