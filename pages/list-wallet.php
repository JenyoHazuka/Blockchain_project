<?php include '../include/header.php'; ?>
<div class="container">
    <h1>Liste de Wallet enregistré</h1>
    <table id="dataTable" class="listTable">
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
    <div id="pagination">
        <button id="prevPage">&larr;</button>
        <span id="pageInfo">Page 1 sur 1</span>
        <button id="nextPage">&rarr;</button>
    </div>
</div>

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
        displayTable(1); // Afficher la première page de résultats
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
</script>
<?php include '../include/footer.php'; ?>