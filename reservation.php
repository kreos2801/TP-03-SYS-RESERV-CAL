<?php 
session_start();
require "config.php";

// Vérification connexion utilisateur
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php"); 
    exit;
}

$user_id = $_SESSION["user_id"];

// Récupération des infos utilisateur
$stmt = $pdo->prepare("SELECT nom, prenom, email, telephone FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit;
}

// Gestion du mois et de l'année
$currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

if ($currentMonth < 1) {
    $currentMonth = 12;
    $currentYear--;
} elseif ($currentMonth > 12) {
    $currentMonth = 1;
    $currentYear++;
}

$daysInMonth = date('t', strtotime("$currentYear-$currentMonth-01"));
$startDay = (date('N', strtotime("$currentYear-$currentMonth-01")) % 7);

$prevMonth = $currentMonth - 1;
$prevYear = $currentYear;
$nextMonth = $currentMonth + 1;
$nextYear = $currentYear;

if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

// Définition des créneaux horaires
$availableSlots = [
    '08:00' => '08h-09h',
    '09:00' => '09h-10h',
    '10:00' => '10h-11h',
    '11:00' => '11h-12h',
    '14:00' => '14h-15h',
    '15:00' => '15h-16h',
    '16:00' => '16h-17h',
    '17:00' => '17h-18h'
];

include 'includes/header.php'; 
?>

<h2 class="text-center">Réservez votre créneau</h2>

<?php if (!empty($message)): ?>
    <div id="successMessage" class="alert alert-success text-center">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div class="container">
    <div class="d-flex justify-content-between">
        <a href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" class="btn btn-outline-primary">← Mois précédent</a>
        <h3><?php echo (new DateTime("$currentYear-$currentMonth-01"))->format('F Y'); ?></h3>
        <a href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" class="btn btn-outline-primary">Mois suivant →</a>
    </div>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <?php 
                $dayNames = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
                foreach ($dayNames as $dayName) {
                    echo "<th>$dayName</th>";
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <tr>
            <?php
            $dayCounter = 1;
            $today = date('Y-m-d');
            $dayOfWeek = $startDay; // Suivi du jour de la semaine

            for ($i = 1; $i < $startDay; $i++) {
                echo '<td></td>';
            }

            while ($dayCounter <= $daysInMonth) {
                if ($dayOfWeek == 1) echo '<tr>'; // Début de semaine (lundi)
                
                $currentDate = "$currentYear-$currentMonth-" . str_pad($dayCounter, 2, '0', STR_PAD_LEFT);
                $isPast = (strtotime($currentDate) < strtotime($today));
                $cellClass = $isPast ? 'bg-secondary text-white' : '';

                echo "<td class='$cellClass'>";
                echo "<div class='day' data-date='$currentDate'>$dayCounter</div>";

                if ($isPast) {
                    echo "<div class='text-center'><small>Indisponible</small></div>";
                } else {
                    echo "<div class='row'><div class='col'><h6>Matin</h6>";
                    foreach (array_slice($availableSlots, 0, 4) as $heure => $label) {
                        echo "<button class='btn btn-primary btn-sm slot' data-date='$currentDate' data-heure='$heure'>$label</button>";
                    }
                    echo "</div><div class='col'><h6>Après-midi</h6>";
                    foreach (array_slice($availableSlots, 4, 4) as $heure => $label) {
                        echo "<button class='btn btn-primary btn-sm slot' data-date='$currentDate' data-heure='$heure'>$label</button>";
                    }
                    echo "</div></div>";
                }
                echo "</td>";

                if ($dayOfWeek == 7) echo '</tr>'; // Fin de semaine (dimanche)

                $dayCounter++;
                $dayOfWeek = ($dayOfWeek % 7) + 1;
            }
            ?>
            </tr>
        </tbody>
    </table>
</div>

<!-- Formulaire de réservation -->
<form method="POST" action="reservation.php" id="reservationForm" style="display:none;">
    <input type="hidden" name="date" id="reservationDate">
    <input type="hidden" name="heure" id="reservationHeure">

    <p id="selectedDate"></p> 

    <button type="submit" class="btn btn-success">Confirmer la réservation</button>
</form>

<script>
    $(document).ready(function() {
        $('.slot').on('click', function() {
            let date = $(this).data('date');
            let heure = $(this).data('heure');

            $('#reservationDate').val(date);
            $('#reservationHeure').val(heure);
            $('#selectedDate').text('Date sélectionnée: ' + date + ' de ' + heure + ' à ' + (parseInt(heure.split(':')[0]) + 1) + 'h');

            $('#reservationForm').show();
        });

        if ($('#successMessage').length > 0) {
            setTimeout(function() {
                $('#successMessage').fadeOut();
            }, 5000);
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
