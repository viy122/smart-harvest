<?php

header('Content-Type: application/json');
include '../../db_connect.php';

$id = intval($_GET['id'] ?? 0);
$role = trim($_GET['role'] ?? '');

if ($id <= 0 || empty($role)) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    if ($role === 'Admin') {
        // Get from users table
        $stmt = $conn->prepare("SELECT user_id, name, username, email, contact_number, address, is_active FROM users WHERE user_id = ? AND role = 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            echo json_encode([
                'success' => true,
                'person' => [
                    'name' => $row['name'],
                    'username' => $row['username'],
                    'email' => $row['email'],
                    'contact_number' => $row['contact_number'],
                    'address' => $row['address'],
                    'is_active' => $row['is_active']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Admin not found']);
        }

    } elseif ($role === 'Farmer') {
        // Get from farmers table
        $stmt = $conn->prepare("SELECT farmer_id, farmer_name, email, contact_number, address FROM farmers WHERE farmer_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            echo json_encode([
                'success' => true,
                'person' => [
                    'name' => $row['farmer_name'],
                    'username' => null,
                    'email' => $row['email'],
                    'contact_number' => $row['contact_number'],
                    'address' => $row['address']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Farmer not found']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid role']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>