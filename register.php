<?php
session_start();
require "config.php"; // Connexion à la base de données

$message = ""; // Message de confirmation ou d'erreur

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hachage du mot de passe

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger text-center'>Email invalide.</div>";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $password]);
            $message = "<div class='alert alert-success text-center'>Inscription réussie. <a href='login.php'>Connectez-vous</a></div>";
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
