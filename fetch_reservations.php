<?php
require 'config.php';

$stmt = $pdo->prepare("SELECT * FROM reservations");
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($reservations);
?>
