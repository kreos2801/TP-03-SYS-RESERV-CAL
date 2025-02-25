<?php
session_start();
require "config.php"; // Connexion à la base de données

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$message = ""; 
$user_id = $_SESSION['user_id']; // L'utilisateur connecté

// Vérification de la disponibilité du créneau
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $telephone = trim($_POST["telephone"]);
    $date = $_POST["date"];
    $heure = $_POST["heure"];
    $personnes = (int) $_POST["personnes"];
    $message = htmlspecialchars($_POST["message"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format d'email invalide.";
    } else {
        // Vérification de la disponibilité du créneau
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE date_reservation = ? AND heure = ?");
        $stmt->execute([$date, $heure]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $error = "Ce créneau horaire est déjà réservé.";
        } else {
            // Enregistrement de la réservation
            $sql = "INSERT INTO reservations (nom, prenom, email, telephone, date_reservation, heure, personnes, message) 
                    VALUES (:nom, :prenom, :email, :telephone, :date_reservation, :heure, :personnes, :message)";
            
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([ 
                ":nom" => $nom,
                ":prenom" => $prenom,
                ":email" => $email,
                ":telephone" => $telephone,
                ":date_reservation" => $date,
                ":heure" => $heure,
                ":personnes" => $personnes,
                ":message" => $message
            ])) {
                $success = "🎉 Votre réservation a bien été enregistrée !";
            } else {
                $error = "Une erreur est survenue.";
            }
        }
    }
}

include 'includes/header.php'; 
?>

<!-- Contenu spécifique à la page -->
<h2 class="text-center">Réservez votre table</h2>

<!-- Messages d'erreur/succès -->
<?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
<?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<!-- Calendrier interactif -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.3.1/dist/fullcalendar.min.css" rel="stylesheet" />
<!-- Inclure jQuery avant les autres scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.27.0/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.3.1/dist/fullcalendar.min.js"></script>

<div id="calendar"></div>


<!-- Formulaire de réservation -->
<form method="POST" id="reservationForm" style="display:none;">
    <div class="mb-3">
        <label class="form-label">Nom</label>
        <input type="text" name="nom" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Prénom</label>
        <input type="text" name="prenom" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Téléphone</label>
        <input type="tel" name="telephone" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Date</label>
        <input type="date" name="date" class="form-control" required id="reservationDate">
    </div>
    <div class="mb-3">
        <label class="form-label">Heure</label>
        <input type="time" name="heure" class="form-control" required id="reservationTime">
    </div>
    <div class="mb-3">
        <label class="form-label">Nombre de personnes</label>
        <select name="personnes" class="form-select" required>
            <option value="1">1 personne</option>
            <option value="2">2 personnes</option>
            <option value="3">3 personnes</option>
            <option value="4">4 personnes</option>
            <option value="5">5 personnes</option>
            <option value="6+">6 et plus</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Réserver</button>
</form>

<script>
    $(document).ready(function() {
        // Initialisation du calendrier
        $('#calendar').fullCalendar({
            events: function(start, end, timezone, callback) {
                $.ajax({
                    url: 'fetch_reservations.php', // Page PHP pour récupérer les réservations existantes
                    dataType: 'json',
                    success: function(data) {
                        var events = data.map(function(reservation) {
                            return {
                                title: reservation.nom + " " + reservation.prenom,
                                start: reservation.date_reservation + "T" + reservation.heure,
                                end: reservation.date_reservation + "T" + reservation.heure,
                                allDay: false
                            };
                        });
                        callback(events);
                    }
                });
            },
            eventClick: function(event) {
                $('#reservationDate').val(event.start.format('YYYY-MM-DD'));
                $('#reservationTime').val(event.start.format('HH:mm'));
                $('#reservationForm').show();
            }
        });

        // Soumettre le formulaire de réservation
        $('#reservationForm').submit(function(event) {
            event.preventDefault();

            // Vérification de la disponibilité
            $.post('check_availability.php', {
                date: $('#reservationDate').val(),
                time: $('#reservationTime').val()
            }, function(response) {
                if (response.available) {
                    // Enregistrer la réservation
                    $.post('reservation.php', $(this).serialize(), function(data) {
                        alert('Réservation confirmée!');
                        location.reload(); // Rafraîchir la page après la réservation
                    });
                } else {
                    alert('Ce créneau horaire est déjà réservé.');
                }
            }, 'json');
        });
    });
</script>

<?php include 'includes/footer.php'; ?>