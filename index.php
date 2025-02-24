<?php
session_start();
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
    <title>Accueil</title>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- Contenu principal du site -->
<main class="container my-5">
    <section class="text-center">
        <h1>Bienvenue sur notre plateforme</h1>
        <p>Découvrez notre service et réservez en toute simplicité.</p>
        <a href="reservation.php" class="btn btn-primary">Faire une réservation</a>
    </section>

    <section class="mt-5">
        <h2 class="text-center">Nos services</h2>
        <div id="carouselServices" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner text-center">
                <div class="carousel-item active">
                    <img src="assets/img/service1.jpg" class="d-block mx-auto img-fluid" alt="Service 1">
                    <div class="carousel-caption">
                        <h3>Service rapide</h3>
                        <p>Réservez en quelques clics.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="assets/img/service2.jpg" class="d-block mx-auto img-fluid" alt="Service 2">
                    <div class="carousel-caption">
                        <h3>Support client</h3>
                        <p>Un accompagnement personnalisé.</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselServices" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselServices" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
