<?php
include_once '../db_connect.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['farmers']) && !empty($data['farmers'])) {
    foreach ($data['farmers'] as $farmerId) {
        // Example: store to a separate table (create one like "assigned_farmers")
        $stmt = $conn->prepare("INSERT INTO assigned_farmers (farmer_id, assigned_at) VALUES (?, NOW())");
        $stmt->bind_param("i", $farmerId);
        $stmt->execute();
    }

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "No farmers selected"]);
}
?>
