<?php
require "config.php";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Vérifier si le token existe en base
    $stmt = $pdo->prepare("SELECT id FROM users WHERE token = ? AND verified = 0");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Activer le compte
        $stmt = $pdo->prepare("UPDATE users SET verified = 1, token = NULL WHERE id = ?");
        $stmt->execute([$user["id"]]);

        echo "<div class='alert alert-success text-center'>Compte activé avec succès ! <a href='login.php'>Connectez-vous</a></div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Lien invalide ou compte déjà vérifié.</div>";
    }
} else {
    echo "<div class='alert alert-danger text-center'>Aucun token fourni.</div>";
}
?>
