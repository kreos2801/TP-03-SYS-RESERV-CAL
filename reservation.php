<?php
session_start();
require "config.php"; // Connexion à la BDD

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="text-center">Réservez votre table</h2>

    <!-- Affichage des messages -->
    <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="POST">
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
            <input type="date" name="date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Heure</label>
            <input type="time" name="heure" class="form-control" required>
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
        <div class="mb-3">
            <label class="form-label">Demande spéciale</label>
            <textarea name="message" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Réserver</button>
    </form>

    <a href="logout.php" class="btn btn-secondary mt-3">Se déconnecter</a>
</body>
</html>
