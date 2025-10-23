<?php
// add_task.php
// expects a working backend/db_connect.php that sets $conn (MySQLi)
include 'backend/db_connect.php';

// fetch tasks from DB
$tasks = [];
$sql = "SELECT task_id, task_name, description, icon, category FROM tasks ORDER BY task_name";
$res = $conn->query($sql);
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $tasks[] = $r;
    }
}
?>