<?php
// filepath: c:\xampp\htdocs\Agrilink\backend\api\tasks\get_task_details.php
header('Content-Type: application/json');
include '../../db_connect.php';

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("
  SELECT 
    ta.assignment_id,
    ta.status,
    ta.due_date,
    ta.notes,
    ta.crop_type,
    t.task_name,
    t.category,
    u.name as assignee_name,
    f.name as field_name
  FROM task_assignments ta
  JOIN tasks t ON ta.task_id = t.task_id
  LEFT JOIN users u ON ta.assigned_to = u.user_id
  LEFT JOIN fields f ON ta.field_id = f.field_id
  WHERE ta.assignment_id = ?
");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  echo json_encode($row);
} else {
  echo json_encode(['error' => 'Task not found']);
}
?>