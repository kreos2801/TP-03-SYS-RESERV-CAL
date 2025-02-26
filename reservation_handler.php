<?php

require 'config.php';

function getReservedSlots($month, $year) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT date_reservation, heure FROM reservations WHERE MONTH(date_reservation) = ? AND YEAR(date_reservation) = ?");
    $stmt->execute([$month, $year]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function isSlotReserved($date, $heure) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE date_reservation = ? AND heure = ?");
    $stmt->execute([$date, $heure]);
    return $stmt->fetchColumn() > 0;
}

function createReservation($user_id, $nom, $prenom, $email, $telephone, $date, $heure) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO reservations (user_id, nom, prenom, email, telephone, date_reservation, heure) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $nom, $prenom, $email, $telephone, $date, $heure]);
}

?>
