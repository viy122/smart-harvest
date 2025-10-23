<?php
include '../../db_connect.php';

$farmer_id = 1; // example only — replace with $_SESSION['farmer_id'] when you add login

// ✅ Get crops that the farmer already has in any of their fields
$onFarmQuery = "
    SELECT 
        c.crop_id,
        c.crop_name,
        c.image_path,
        fc.field_id
    FROM crops c
    INNER JOIN field_crops fc ON c.crop_id = fc.crop_id
    INNER JOIN fields f ON fc.field_id = f.field_id
    WHERE f.farmer_id = $farmer_id
";

$onFarm = $conn->query($onFarmQuery);

// ✅ Get crops that are NOT yet assigned to any field of this farmer
$notOnFarmQuery = "
    SELECT 
        c.crop_id,
        c.crop_name,
        c.image_path
    FROM crops c
    WHERE c.crop_id NOT IN (
        SELECT fc.crop_id
        FROM field_crops fc
        INNER JOIN fields f ON fc.field_id = f.field_id
        WHERE f.farmer_id = $farmer_id
    )
";

$notOnFarm = $conn->query($notOnFarmQuery);

// ✅ Prepare JSON response
$response = [
    'onFarm' => $onFarm ? $onFarm->fetch_all(MYSQLI_ASSOC) : [],
    'notOnFarm' => $notOnFarm ? $notOnFarm->fetch_all(MYSQLI_ASSOC) : []
];

header('Content-Type: application/json');
echo json_encode($response);
?>
