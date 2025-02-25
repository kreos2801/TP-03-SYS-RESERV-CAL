<?php
require 'config.php';

$date = $_POST['date'];
$time = $_POST['time'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE date_reservation = ? AND heure = ?");
$stmt->execute([$date, $time]);
$count = $stmt->fetchColumn();

echo json_encode(['available' => $count == 0]);
?>
