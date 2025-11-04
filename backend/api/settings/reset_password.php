<?php

header('Content-Type: application/json');
include '../../db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);

$userId = intval($input['user_id'] ?? 0);
$password = trim($input['password'] ?? '');

if ($userId <= 0 || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit;
}

try {
    // Hash the new password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Update password in users table
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ? AND role = 1");
    $stmt->bind_param("si", $hashedPassword, $userId);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Password reset successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to reset password']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>