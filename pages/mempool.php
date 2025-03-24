<?php include '../include/header.php'; ?>
<main>
<div class="container">
    <h1>Mempool</h1>
    <table id="dataTableMempool" class="listTable">
        <thead>
            <tr>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Amount</th>
                <th>Fees</th>
                <th>Timestamp</th>
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
</main>

<script>
    // Récupérer les données via AJAX (fetch) depuis mempool-be.php
    fetch('../src/mempool-be.php')
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur de réseau : ' + response.status);
        }
        return response.json();
    })
    .then(mempoolTable => {
        // Passer les données dans le script JavaScript
        window.mempoolTable = mempoolTable; // Sauvegarder dans une variable globale
        displayTable(1); // Afficher la première page de résultats
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
</script>
<?php include '../include/footer.php'; ?>