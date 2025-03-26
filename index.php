<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/forge/0.10.0/forge.min.js"></script>
    <script src="js/script.js"></script>
    <title>Projet Blockchain</title>
</head>
<body>
<header>
    <h1>Projet Blockchain</h1>
    <nav>
        <ul>
            <li><a href="pages/hash-cryptage.php">Hashage et Cryptage</a></li>
            <li><a href="pages/wallet.php">Wallet</a></li>
            <li><a href="pages/list-wallet.php">Listes Wallets</a></li>
            <li><a href="pages/transaction.php">Transaction</a></li>
            <li><a href="pages/mining.php">Mining</a></li>
            <li><a href="pages/block.php">Block</a></li>
            <li><a href="pages/cours.php">Cours</a></li>
        </ul>
    </nav>
</header>
<main>
    <h1>Site pour tester le fonctionnement d'une Blockchain</h1>
    <h3 styles="margin-top: 20px">Sélection d'un module : </h3>
    <section class="cards-container">
        <div class="card">
            <a href="pages/hash-cryptage.php" class="card-link">
                <h2 class="card-title">1. Hashage et Chiffrage</h2>
            </a>
        </div>
        <div class="card">
            <a href="pages/wallet.php" class="card-link">
                <h2 class="card-title">2. Créer un Wallet</h2>
            </a>
        </div>
        <div class="card">
            <a href="pages/list-wallet.php" class="card-link">
                <h2 class="card-title">3. Liste des wallets</h2>
            </a>
        </div>
        <div class="card">
            <a href="pages/transaction.php" class="card-link">
                <h2 class="card-title">4. Faire une transaction</h2>
            </a>
        </div>
    </section>
    <section class="cards-container">
        <div class="card">
            <a href="pages/mining.php" class="card-link">
                <h2 class="card-title">6. Minage de bloc</h2>
            </a>
        </div>
        <div class="card">
            <a href="pages/block.php" class="card-link">
                <h2 class="card-title">7. Recherche de blocs</h2>
            </a>
        </div>
        <div class="card">
            <a href="pages/cours.php" class="card-link">
                <h2 class="card-title">8. Cours de Blockchain</h2>
            </a>
        </div>
    </section>
</main>
<?php include 'include/footer.php'; ?>
