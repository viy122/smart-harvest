<?php
header('Content-Type: application/json');
include '../../db_connect.php';

// Fetch all active or existing tasks
$query = "SELECT task_id, task_name, description, icon, status, created_at 
          FROM tasks
          ORDER BY created_at DESC";

$result = mysqli_query($conn, $query);

$tasks = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $tasks[] = [
            'task_id' => (int)$row['task_id'],
            'task_name' => $row['task_name'],
            'description' => $row['description'] ?? '',
            'icon' => $row['icon'] ?? '',
            'status' => $row['status'] ?? 'active',
            'created_at' => $row['created_at']
        ];
    }
}

// Return as JSON
echo json_encode($tasks);
mysqli_close($conn);
?>
