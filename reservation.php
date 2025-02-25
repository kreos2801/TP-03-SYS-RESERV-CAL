<?php
session_start();
require "config.php";

// Vérification que l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php"); // Redirige vers la page de connexion si non connecté
    exit;
}

$user_id = $_SESSION["user_id"];

// Récupération des informations de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT nom, prenom, email, telephone FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit;
}

$message = ""; // Variable pour afficher le message

// Vérification si le formulaire a bien été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire
    $date = $_POST["date"] ?? null;
    $heure = $_POST["heure"] ?? null;

    // Validation des données
    if (empty($date) || empty($heure)) {
        $message = "Veuillez choisir une date et une heure.";
    } else {
        // Vérification de la disponibilité du créneau
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE date_reservation = ? AND heure = ?");
        $stmt->execute([$date, $heure]);
        if ($stmt->fetchColumn() > 0) {
            $message = "Ce créneau est déjà réservé.";
        } else {
            // Enregistrement de la réservation dans la base de données
            $sql = "INSERT INTO reservations (user_id, nom, prenom, email, telephone, date_reservation, heure) 
                    VALUES (:user_id, :nom, :prenom, :email, :telephone, :date_reservation, :heure)";
            $stmt = $pdo->prepare($sql);
            
            // Exécution de la requête d'insertion
            if ($stmt->execute([
                ":user_id" => $user_id,
                ":nom" => $user["nom"],
                ":prenom" => $user["prenom"],
                ":email" => $user["email"],
                ":telephone" => $user["telephone"],
                ":date_reservation" => $date,
                ":heure" => $heure
            ])) {
                $message = "🎉 Votre réservation a bien été prise en compte !";
            } else {
                $message = "Erreur lors de la réservation.";
            }
        }
    }
}

include 'includes/header.php'; // Inclut l'en-tête du site
?>

<!-- Affichage du calendrier -->
<h2 class="text-center">Réservez votre créneau</h2>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.3.1/dist/fullcalendar.min.css" rel="stylesheet" />
<link rel="stylesheet" href="assets/css/style.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.27.0/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.3.1/dist/fullcalendar.min.js"></script>

<?php if ($message): ?>
    <div id="successMessage" class="alert alert-success text-center">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<div id="calendar"></div>

<form method="POST" action="reservation.php" id="reservationForm" style="display:none;">
    <input type="hidden" name="date" id="reservationDate">
    
    <p id="selectedDate"></p> 

    <div class="mb-3">
        <label class="form-label">Choisissez une heure :</label>
        <input type="time" name="heure" class="form-control" required id="reservationTime">
    </div>

    <button type="submit" class="btn btn-primary">Réserver</button>
</form>

<script>
    $(document).ready(function() {
    $('#calendar').fullCalendar({
        events: function(start, end, timezone, callback) {
            $.ajax({
                url: 'fetch_reservations.php', // Charge les réservations existantes
                dataType: 'json',
                success: function(data) {
                    let events = data.map(function(reservation) {
                        return {
                            title: "Réservé",
                            start: reservation.date_reservation,
                            rendering: 'background', // Affiche le créneau comme indisponible
                            backgroundColor: '#d9534f' // Rouge pour indiquer "indisponible"
                        };
                    });
                    callback(events);
                }
            });
        },
        dayClick: function(date, jsEvent, view) {
            let today = moment().startOf('day');

            if (date.isBefore(today)) {
                alert("Vous ne pouvez pas réserver une date passée !");
                return;
            }

            let isDisabled = $('#calendar').fullCalendar('clientEvents', function(event) {
                return event.start.isSame(date, 'day') && event.rendering === 'background';
            }).length > 0;

            if (isDisabled) {
                alert("Cette date est déjà réservée !");
                return;
            }

            // Mettre à jour le champ caché avec la date sélectionnée
            $('#reservationDate').val(date.format('YYYY-MM-DD'));

            // Afficher la date sélectionnée sous forme lisible dans le formulaire
            $('#selectedDate').text('Date sélectionnée: ' + date.format('dddd, D MMMM YYYY'));

            // Afficher le formulaire
            $('#reservationForm').show();
        }
    });

    // Masquer le message après 5 secondes
    if ($('#successMessage').length > 0) {
        setTimeout(function() {
            $('#successMessage').fadeOut();
        }, 5000);
    }
});
</script>

<?php include 'includes/footer.php'; ?>
