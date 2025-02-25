<?php
session_start();
require 'config.php';

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "Utilisateur non connecté"]);
    exit;
}

$user_id = $_SESSION["user_id"];
$reservation_id = $_POST["reservation_id"];

// Vérification que la réservation appartient bien à l’utilisateur
$stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ? AND user_id = ?");
$stmt->execute([$reservation_id, $user_id]);
$reservation = $stmt->fetch();

if (!$reservation) {
    echo json_encode(["error" => "Réservation introuvable"]);
    exit;
}

// Suppression de la réservation
$stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
if ($stmt->execute([$reservation_id])) {
    echo json_encode(["success" => "Réservation annulée"]);
} else {
    echo json_encode(["error" => "Erreur lors de l'annulation"]);
}
