<?php
session_start();
require "config.php"; // Connexion à la base de données

$message = ""; 
$user_id = $_SESSION['user_id']; // L'utilisateur connecté

// Récupération des données actuelles
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = isset($_POST["nom"]) ? trim($_POST["nom"]) : $user['nom'];
    $prenom = isset($_POST["prenom"]) ? trim($_POST["prenom"]) : $user['prenom'];
    $email = isset($_POST["email"]) ? filter_var($_POST["email"], FILTER_SANITIZE_EMAIL) : $user['email'];
    $date_naissance = isset($_POST["date_naissance"]) ? $_POST["date_naissance"] : $user['date_naissance'];
    $adresse_postale = isset($_POST["adresse_postale"]) ? trim($_POST["adresse_postale"]) : $user['adresse_postale'];
    $password = isset($_POST["password"]) && !empty($_POST["password"]) ? password_hash($_POST["password"], PASSWORD_DEFAULT) : $user['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='alert alert-danger text-center'>Email invalide.</div>";
    } else {
        // Vérification de l'unicité de l'email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        $email_exists = $stmt->fetchColumn();

        if ($email_exists > 0) {
            $message = "<div class='alert alert-danger text-center'>L'email est déjà utilisé par un autre utilisateur.</div>";
        } else {
            try {
                // Mise à jour des informations utilisateur
                $stmt = $pdo->prepare("UPDATE users SET nom = ?, prenom = ?, email = ?, date_naissance = ?, adresse_postale = ?, password = ? WHERE id = ?");
                $stmt->execute([$nom, $prenom, $email, $date_naissance, $adresse_postale, $password, $user_id]);

                $message = "<div class='alert alert-success text-center'>Informations mises à jour avec succès.</div>";
            } catch (PDOException $e) {
                $message = "<div class='alert alert-danger text-center'>Erreur : " . $e->getMessage() . "</div>";
            }
        }
    }
}

// Suppression du compte
if (isset($_POST['delete_account'])) {
    try {
        // Suppression des réservations associées à l'utilisateur
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Suppression du compte utilisateur
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        // Déconnexion
        session_unset();
        session_destroy();

        header("Location: goodbye.php"); // Redirection vers une page de confirmation
        exit;
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger text-center'>Erreur lors de la suppression du compte : " . $e->getMessage() . "</div>";
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
    <title>Paramètres</title>
</head>
<body class="bg-light"> <!-- Fond clair -->

<?php include 'includes/header.php'; ?>

<main class="container my-5">
    <section class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg bg-white"> <!-- Fond blanc -->
                <div class="card-body">
                    <h2 class="text-center mb-4 text-dark">Paramètres de Compte</h2> 

                    <?php echo $message; ?> <!-- Affichage des messages -->

                    <form method="POST">
                        <div class="mb-3">
                            <label for="nom" class="form-label text-dark">Nom</label>
                            <input type="text" name="nom" id="nom" class="form-control bg-white text-dark" value="<?= htmlspecialchars($user['nom']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="prenom" class="form-label text-dark">Prénom</label>
                            <input type="text" name="prenom" id="prenom" class="form-control bg-white text-dark" value="<?= htmlspecialchars($user['prenom']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label text-dark">E-mail</label>
                            <input type="email" name="email" id="email" class="form-control bg-white text-dark" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="date_naissance" class="form-label text-dark">Date de naissance</label>
                            <input type="date" name="date_naissance" id="date_naissance" class="form-control bg-white text-dark" value="<?= htmlspecialchars($user['date_naissance']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="adresse_postale" class="form-label text-dark">Adresse postale</label>
                            <input type="text" name="adresse_postale" id="adresse_postale" class="form-control bg-white text-dark" value="<?= htmlspecialchars($user['adresse_postale']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label text-dark">Nouveau mot de passe (facultatif)</label>
                            <input type="password" name="password" id="password" class="form-control bg-white text-dark" placeholder="Nouveau mot de passe">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary w-100">Mettre à jour</button>
                        </div>
                    </form>

                    <form method="POST" class="mt-4">
                        <div class="text-center">
                            <button type="submit" name="delete_account" class="btn btn-danger w-100">Supprimer mon compte</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
