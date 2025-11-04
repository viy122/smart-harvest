<?php

header('Content-Type: application/json');
include '../../db_connect.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (empty($input['name']) || empty($input['role'])) {
    echo json_encode(['success' => false, 'message' => 'Name and role are required']);
    exit;
}

$name = trim($input['name']);
$email = trim($input['email'] ?? '');
$contact = trim($input['contact'] ?? '');
$address = trim($input['address'] ?? '');
$role = strtolower(trim($input['role']));

try {
    if ($role === 'admin') {
        // Admin - Insert into users table
        $username = trim($input['username'] ?? '');
        $password = trim($input['password'] ?? '');

        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Username and password are required for admin']);
            exit;
        }

        // Check if username already exists
        $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
        $checkStmt->bind_param("s", $username);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            exit;
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert admin
        $stmt = $conn->prepare("INSERT INTO users (name, username, email, contact_number, address, password, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, 1, 1, NOW())");
        $stmt->bind_param("ssssss", $name, $username, $email, $contact, $address, $hashedPassword);

    } elseif ($role === 'farmer') {
        // Farmer - Insert into farmers table
        $stmt = $conn->prepare("INSERT INTO farmers (farmer_name, contact_number, email, address, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $name, $contact, $email, $address);

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid role']);
        exit;
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => ucfirst($role) . ' added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add ' . $role]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>