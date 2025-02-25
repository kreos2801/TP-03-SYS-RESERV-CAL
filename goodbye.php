<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Compte supprimé</title>
</head>
<body class="bg-light">

<?php include 'includes/header.php'; ?>

<main class="container my-5">
    <section class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg bg-white">
                <div class="card-body">
                    <h2 class="text-center text-dark">Votre compte a été supprimé</h2>
                    <p class="text-center text-dark">Nous sommes désolés de vous voir partir. Votre compte a bien été supprimé ainsi que toutes les données associées.</p>
                    <div class="text-center">
                        <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
                        <a href="login.php" class="btn btn-secondary">Se reconnecter</a>
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
