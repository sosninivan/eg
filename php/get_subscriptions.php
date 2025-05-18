<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM subscriptions WHERE user_id = ?");
$stmt->execute([$user_id]);
$subs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$today = new DateTime();

foreach ($subs as &$sub) {
    $start = new DateTime($sub['start_date']);
    $end = new DateTime($sub['end_date']);

    $total_days = $start->diff($end)->days;
    $days_left = max(0, $today->diff($end)->days);
    $used = $sub['used_trainings'];
    $total = $sub['total_trainings'];
    $percent = $total > 0 ? round(($used / $total) * 100) : 0;

    $sub['days_left'] = $days_left;
    $sub['percent_used'] = $percent;
    $sub['remaining_trainings'] = max($total - $used, 0);
}

echo json_encode($subs);
