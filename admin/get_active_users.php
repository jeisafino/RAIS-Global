<?php
require_once '../config.php';

$active_threshold = 15; // in seconds

$sql_active = "SELECT COUNT(id) AS active_count FROM users WHERE role = 'client' AND last_activity >= NOW() - INTERVAL ? SECOND";
$stmt_active = $conn->prepare($sql_active);
$stmt_active->bind_param("i", $active_threshold);
$stmt_active->execute();
$result_active = $stmt_active->get_result();
$active_row = $result_active->fetch_assoc();
$active_users = $active_row['active_count'];
$stmt_active->close();

$sql_total = "SELECT COUNT(id) AS total_count FROM users WHERE role = 'client'";
$result_total = $conn->query($sql_total);
$total_row = $result_total->fetch_assoc();
$total_users = $total_row['total_count'];

$inactive_users = $total_users - $active_users;

$conn->close();

header('Content-Type: application/json');
echo json_encode([
    'active_users' => $active_users,
    'inactive_users' => $inactive_users
]);
?>