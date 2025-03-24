<?php include '../include/header.php'; ?>
<main>
<div class="container">
    <h1>Mining de block</h1>

    <form id="miningForm">
        <label for="wallet">Choisir un wallet :</label>
        <select id="wallet" name="wallet"></select>

        <label for="hash_prec">Hash précédent :</label>
        <input type="text" id="hash_prec" name="hash_prec" readonly>

        <label for="version">Version :</label>
        <input type="text" id="version" name="version" value="1.0">

        <label for="target">Difficulté (Bits) :</label>
        <input type="number" id="target" name="target" value="2">

        <label for="nonce">Nonce :</label>
        <input type="number" id="nonce" name="nonce" value="0" readonly>

        <label for="merkle_root">Merkle Root :</label>
        <input type="text" id="merkle_root" name="merkle_root" readonly>

        <button type="button" id="mineBlock">Miner le block</button>
    </form>

    <h2>Transactions en attente</h2>
    <table>
        <thead>
            <tr>
                <th>Sélection</th>
                <th>ID</th>
                <th>Expéditeur</th>
                <th>Destinataire</th>
                <th>Montant</th>
                <th>Frais</th>
            </tr>
        </thead>
        <tbody id="transactionsTable"></tbody>
    </table>
</div>
</main>
<?php include '../include/footer.php'; ?>