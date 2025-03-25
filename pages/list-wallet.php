<?php include '../include/header.php'; ?>
<main>
<div class="container">
    <h1>Liste de Wallet enregistré</h1>
    <table id="dataTableWallets" class="listTable">
        <thead>
            <tr>
                <th>Wallet Name</th>
                <th>Public Key</th>
                <th>Private Key</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <!-- Les lignes du tableau seront insérées ici via JavaScript -->
        </tbody>
    </table>
    <div id="paginationWallet">
        <button id="prevPageWallet">&larr;</button>
        <span id="pageInfoWallet">Page 1 sur 1</span>
        <button id="nextPageWallet">&rarr;</button>
    </div>
</div>
</main>

<script>
    // Récupérer les données via AJAX (fetch) depuis list-wallet-be.php
    fetch('../src/list-wallet-be.php')
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur de réseau : ' + response.status);
        }
        return response.json();
    })
    .then(walletTable => {
        // Passer les données dans le script JavaScript
        window.walletTable = walletTable; // Sauvegarder dans une variable globale
        console.log(window.walletTable);
        displayTableWallet(1); // Afficher la première page de résultats
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
</script>
<?php include '../include/footer.php'; ?>