<?php
session_start();
require "config.php";

// Vérification connexion utilisateur
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php"); 
    exit;
}

$user_id = $_SESSION["user_id"];

// Suppression d'une réservation avec confirmation
if (isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ? AND user_id = ?");
    $stmt->execute([$_POST['delete_id'], $user_id]);
}

// Modification d'une réservation avec vérification de disponibilité
if (isset($_POST['update_id'], $_POST['new_date'], $_POST['new_time'])) {
    // Vérifier si le créneau est disponible
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE date_reservation = ? AND heure = ?");
    $stmt->execute([$_POST['new_date'], $_POST['new_time']]);
    if ($stmt->fetchColumn() == 0) {
        $stmt = $pdo->prepare("UPDATE reservations SET date_reservation = ?, heure = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$_POST['new_date'], $_POST['new_time'], $_POST['update_id'], $user_id]);
    } else {
        echo "<script>alert('Le créneau sélectionné est déjà réservé. Veuillez en choisir un autre.');</script>";
    }
}

// Récupération des réservations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ? ORDER BY date_reservation, heure");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<h2 class="text-center">Mes Réservations</h2>

<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Heure</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td><?= htmlspecialchars($reservation['date_reservation']) ?></td>
                    <td><?= htmlspecialchars($reservation['heure']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?');">
                            <input type="hidden" name="delete_id" value="<?= $reservation['id'] ?>">
                            <button type="submit" class="btn btn-danger">Annuler</button>
                        </form>
                        
                        <button class="btn btn-warning btn-modify" 
                                data-id="<?= $reservation['id'] ?>" 
                                data-date="<?= $reservation['date_reservation'] ?>" 
                                data-time="<?= $reservation['heure'] ?>">
                            Modifier
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal de modification -->
<div id="modalModify" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier la Réservation</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="update_id" id="updateId">
                    <label for="newDate">Nouvelle Date</label>
                    <input type="date" name="new_date" id="newDate" class="form-control" required>
                    <label for="newTime">Nouvelle Heure</label>
                    <input type="time" name="new_time" id="newTime" class="form-control" required>
                    <button type="submit" class="btn btn-success mt-2">Confirmer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-modify').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('updateId').value = this.dataset.id;
            document.getElementById('newDate').value = this.dataset.date;
            document.getElementById('newTime').value = this.dataset.time;
            new bootstrap.Modal(document.getElementById('modalModify')).show();
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
