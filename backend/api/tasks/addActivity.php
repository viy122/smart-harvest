<?php
header("Content-Type: application/json");
include_once("../db_connect.php"); // adjust path kung needed

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from form or JS fetch()
    $crop_id = $_POST['crop_id'] ?? null;
    $activity_type = $_POST['activity_type'] ?? '';
    $date = $_POST['date'] ?? '';
    $status = $_POST['status'] ?? 'Pending';
    $notes = $_POST['notes'] ?? '';

    // Basic validation
    if (!$crop_id || !$activity_type || !$date) {
        $response = [
            "success" => false,
            "message" => "Please fill in all required fields."
        ];
    } else {
        // Insert to DB
        $query = "INSERT INTO crop_activities (crop_id, activity_type, date, status, notes) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issss", $crop_id, $activity_type, $date, $status, $notes);

        if ($stmt->execute()) {
            $response = [
                "success" => true,
                "message" => "Activity added successfully."
            ];
        } else {
            $response = [
                "success" => false,
                "message" => "Database error: " . $conn->error
            ];
        }

        $stmt->close();
    }
} else {
    $response = [
        "success" => false,
        "message" => "Invalid request method."
    ];
}

echo json_encode($response);
$conn->close();
?>
