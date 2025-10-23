<?php
// backend/save_field.php
header("Content-Type: application/json");

// ✅ Connect to your database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "smart_harvest"; // change if your DB name is different

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// ✅ Read the JSON body
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode(["error" => "Invalid input"]);
    exit;
}

// ✅ Sanitize and extract data
$name       = $conn->real_escape_string($input['name']);
$area       = floatval($input['area']);
$perimeter  = floatval($input['perimeter']);
$type       = $conn->real_escape_string($input['type']);
$notes      = $conn->real_escape_string($input['notes']);
$geometry   = $conn->real_escape_string($input['geometry']); // JSON polygon data

// ✅ Create table if not exists (for demo/testing)
$conn->query("
    CREATE TABLE IF NOT EXISTS fields (
        field_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        area DECIMAL(12,2) NOT NULL,
        perimeter DECIMAL(12,2) NOT NULL,
        type ENUM('Organic','Non-organic','Transitioning') NOT NULL,
        notes TEXT,
        geometry JSON NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

// ✅ Insert data
$sql = "INSERT INTO fields (name, area, perimeter, type, notes, geometry)
        VALUES ('$name', $area, $perimeter, '$type', '$notes', '$geometry')";

if ($conn->query($sql)) {
    echo json_encode(["success" => "Field saved successfully!"]);
} else {
    echo json_encode(["error" => "Failed to save: " . $conn->error]);
}

$conn->close();
?>
