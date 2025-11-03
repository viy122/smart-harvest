<?php
header("Content-Type: application/json");
include '../../db_connect.php'; // Uses your MySQLi $conn

class CropController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // 🟢 Add a new crop
    public function addCrop($data) {
        // Validate JSON
        if (!is_array($data)) {
            return $this->response(false, "Invalid request format. JSON expected.");
        }

        // Extract fields
        $crop_name   = trim($data['crop_name'] ?? '');
        $description = trim($data['description'] ?? '');
        $category    = trim($data['category'] ?? '');
        $duration    = intval($data['duration'] ?? 0);
        $image_path  = trim($data['image_path'] ?? '');

        // Validate inputs
        if ($crop_name === '' || $duration <= 0) {
            return $this->response(false, "Crop name and valid duration are required.");
        }

        // Check for duplicate crop name
        $check = $this->conn->prepare("SELECT crop_id FROM crops WHERE crop_name = ?");
        $check->bind_param("s", $crop_name);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            return $this->response(false, "Crop name already exists.");
        }

        // Insert new crop
        $stmt = $this->conn->prepare("
            INSERT INTO crops (crop_name, description, image_path, category, duration)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssi", $crop_name, $description, $image_path, $category, $duration);

        if ($stmt->execute()) {
            return $this->response(true, "New crop added successfully!", [
                "crop_id" => $stmt->insert_id
            ]);
        } else {
            return $this->response(false, "Database insert failed: " . $stmt->error);
        }
    }

    // Helper for consistent JSON responses
    private function response($success, $message, $extra = []) {
        return array_merge([
            "success" => $success,
            "message" => $message
        ], $extra);
    }
}

// 🧠 MAIN EXECUTION
$controller = new CropController($conn);

// Decode JSON body
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["success" => false, "message" => "Invalid JSON body."]);
    exit;
}

// Handle add crop
echo json_encode($controller->addCrop($input));
?>
