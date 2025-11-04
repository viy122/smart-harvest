<?php

header('Content-Type: application/json');
include '../../db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);

$id = intval($input['id'] ?? 0);
$role = trim($input['role'] ?? '');
$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$contact = trim($input['contact'] ?? '');
$address = trim($input['address'] ?? '');

if ($id <= 0 || empty($role) || empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    if ($role === 'Admin') {
        // Update users table
        $username = trim($input['username'] ?? '');

        if (empty($username)) {
            echo json_encode(['success' => false, 'message' => 'Username is required for admin']);
            exit;
        }

        // Check if username exists for other users
        $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? AND user_id != ?");
        $checkStmt->bind_param("si", $username, $id);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE users SET name = ?, username = ?, email = ?, contact_number = ?, address = ? WHERE user_id = ? AND role = 1");
        $stmt->bind_param("sssssi", $name, $username, $email, $contact, $address, $id);

    } elseif ($role === 'Farmer') {
        // Update farmers table
        $stmt = $conn->prepare("UPDATE farmers SET farmer_name = ?, email = ?, contact_number = ?, address = ? WHERE farmer_id = ?");
        $stmt->bind_param("ssssi", $name, $email, $contact, $address, $id);

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid role']);
        exit;
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Person updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update person']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>