<?php 
session_start();
require "config.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT id, password, verified FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifie si l'utilisateur existe et s'il est vérifié
    if ($user) {
        if ($user["verified"] == 0) {
            $error_message = "Votre compte n'est pas encore vérifié. Veuillez vérifier votre e-mail.";
        } elseif (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"]; // Stocker l'ID utilisateur
            header("Location: reservation.php"); // Rediriger vers la page de réservation
            exit;
        } else {
            $error_message = "Identifiants incorrects.";
        }
    } else {
        $error_message = "Identifiants incorrects.";
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
    <title>Connexion</title>
</head>
<body class="bg-light"> <!-- Ajout d'un fond clair -->

<?php include 'includes/header.php'; ?>

<main class="container my-5">
    <section class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg bg-white"> <!-- Fond blanc pour contraste -->
                <div class="card-body">
                    <h2 class="text-center mb-4 text-dark">Connexion</h2> <!-- Texte sombre -->

                    <?php if (!empty($error_message)) : ?>
                        <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label text-dark">E-mail</label>
                            <input type="email" name="email" id="email" class="form-control bg-white text-dark" placeholder="Votre e-mail" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label text-dark">Mot de passe</label>
                            <input type="password" name="password" id="password" class="form-control bg-white text-dark" placeholder="Votre mot de passe" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <p class="text-dark">Pas encore de compte ? <a href="register.php">Inscrivez-vous ici</a></p>
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
