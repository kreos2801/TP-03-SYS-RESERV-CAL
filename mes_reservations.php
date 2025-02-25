<?php
session_start();
require 'config.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$stmt = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ?");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<h2>Mes réservations</h2>
<table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Heure</th>
            <th>Personnes</th>
            <th>Message</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reservations as $res): ?>
            <tr>
                <td><?= htmlspecialchars($res['date_reservation']) ?></td>
                <td><?= htmlspecialchars($res['heure']) ?></td>
                <td><?= htmlspecialchars($res['personnes']) ?></td>
                <td><?= htmlspecialchars($res['message']) ?></td>
                <td>
                    <form method="POST" action="annuler_reservation.php">
                        <input type="hidden" name="reservation_id" value="<?= $res['id'] ?>">
                        <button type="submit" class="btn btn-danger">Annuler</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
