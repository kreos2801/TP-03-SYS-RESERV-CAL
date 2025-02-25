<?php
session_start();
require "config.php"; // Connexion à la base de données
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $date_naissance = $_POST["date_naissance"];
    $adresse_postale = trim($_POST["adresse_postale"]);
    $telephone = trim($_POST["telephone"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32)); // Génération d'un token unique

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger text-center'>Email invalide.</div>";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, date_naissance, adresse_postale, password, token, telephone) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $date_naissance, $adresse_postale, $password, $token, $telephone]);

            // Envoi de l'e-mail de vérification
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USER;
                $mail->Password = SMTP_PASS;
                $mail->SMTPSecure = "tls";
                $mail->Port = SMTP_PORT;

                $mail->setFrom(SMTP_FROM, "Grec d'Or");
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = "Vérification de votre compte";
                $mail->Body = "Bonjour $prenom,<br><br>
                               Merci de vous être inscrit ! Cliquez sur le lien ci-dessous pour vérifier votre compte :<br>
                               <a href='http://localhost/TP-03-SYS-RESERV-CAL/verify.php?token=$token'>Vérifier mon compte</a><br><br>
                               Si vous n'avez pas fait cette demande, ignorez cet e-mail.";

                $mail->send();
                $message = "<div class='alert alert-success text-center'>Inscription réussie ! Vérifiez votre e-mail.</div>";
            } catch (Exception $e) {
                $message = "<div class='alert alert-danger text-center'>Erreur d'envoi d'e-mail : {$mail->ErrorInfo}</div>";
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger text-center'>Erreur : " . $e->getMessage() . "</div>";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Inscription</title>
</head>
<body class="bg-light"> <!-- Fond clair -->

<?php include 'includes/header.php'; ?>

<main class="container my-5">
    <section class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg bg-white"> <!-- Fond blanc -->
                <div class="card-body">
                    <h2 class="text-center mb-4 text-dark">Inscription</h2> 

                    <?php echo $message; ?> <!-- Affichage des messages -->

                    <form method="POST">
                        <div class="mb-3">
                            <label for="nom" class="form-label text-dark">Nom</label>
                            <input type="text" name="nom" id="nom" class="form-control bg-white text-dark" placeholder="Votre nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="prenom" class="form-label text-dark">Prénom</label>
                            <input type="text" name="prenom" id="prenom" class="form-control bg-white text-dark" placeholder="Votre prénom" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label text-dark">E-mail</label>
                            <input type="email" name="email" id="email" class="form-control bg-white text-dark" placeholder="Votre e-mail" required>
                        </div>
                        <div class="mb-3">
                            <label for="telephone" class="form-label text-dark">Numéro de téléphone</label>
                            <input type="tel" name="telephone" id="telephone" class="form-control bg-white text-dark" placeholder="Votre numéro de téléphone" required>
                        </div>
                        <div class="mb-3">
                            <label for="date_naissance" class="form-label text-dark">Date de naissance</label>
                            <input type="date" name="date_naissance" id="date_naissance" class="form-control bg-white text-dark" required>
                        </div>
                        <div class="mb-3">
                            <label for="adresse_postale" class="form-label text-dark">Adresse postale</label>
                            <input type="text" name="adresse_postale" id="adresse_postale" class="form-control bg-white text-dark" placeholder="Votre adresse" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label text-dark">Mot de passe</label>
                            <input type="password" name="password" id="password" class="form-control bg-white text-dark" placeholder="Votre mot de passe" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <p class="text-dark">Déjà un compte ? <a href="login.php">Connectez-vous ici</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
