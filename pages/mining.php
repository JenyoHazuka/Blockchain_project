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

        <label for="reward">Récompense de block :</label>
        <input type="number" id="reward" name="reward" value="6.25">

        <label for="nonce">Nonce :</label>
        <input type="number" id="nonce" name="nonce" value="0" readonly>

        <label for="merkle_root">Merkle Root :</label>
        <input type="text" id="merkle_root" name="merkle_root" readonly>

        <button type="button" id="mineBlock">Miner le block</button>
    </form>

    <h2>Transactions en attente</h2>
    <table class="listTable">
        <thead>
            <tr>
                <th>Sélection</th>
                <th>Numéro</th>
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
<script>
document.addEventListener("DOMContentLoaded", function () {
    fetch("../src/load_data_mining.php")
        .then(response => response.json())
        .then(data => {
            let walletSelect = document.getElementById("wallet");
            data.wallets.forEach(wallet => {
                let option = document.createElement("option");
                option.value = wallet.nom_wallet;
                option.textContent = wallet.nom_wallet;
                walletSelect.appendChild(option);
            });

            let transactionsTable = document.getElementById("transactionsTable");
            
            // Trier les transactions par frais (frais_transaction) de manière décroissante
            data.transactions.sort((a, b) => b.frais_transaction - a.frais_transaction);

            data.transactions.forEach(tx => {
                let row = document.createElement("tr");
                row.innerHTML = `
                    <td><input type="checkbox" value="${tx.id_transaction}"></td>
                    <td>${tx.id_transaction}</td>
                    <td>${tx.expediteur_transaction}</td>
                    <td>${tx.destinataire_transaction}</td>
                    <td>${tx.montant_transaction} BTC</td>
                    <td>${tx.frais_transaction}</td>
                `;
                transactionsTable.appendChild(row);
            });

            document.getElementById("hash_prec").value = data.hash_prec;
        })
        .catch(error => console.error("Erreur lors du chargement des données :", error));

    document.getElementById("mineBlock").addEventListener("click", function () {

        // Vérifier si c'est le bloc génésis (hash_prec == 0...0)
        let isGenesisBlock = document.getElementById("hash_prec").value === "0000000000000000000000000000000000000000000000000000000000000000";

        let selectedTx = [];
        if (!isGenesisBlock) {
            selectedTx = [...document.querySelectorAll("#transactionsTable input:checked")].map(input => input.value);
        }

        if (!isGenesisBlock && selectedTx.length === 0) {
            alert("Sélectionnez au moins une transaction.");
            return;
        }

        let blockData = {
            wallet: document.getElementById("wallet").value,
            hash_prec: document.getElementById("hash_prec").value,
            version: document.getElementById("version").value,
            target: parseInt(document.getElementById("target").value),
            reward: parseFloat(document.getElementById("reward").value),
            transactions: selectedTx
        };

        fetch("../src/mining-be.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(blockData)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("Block miné avec succès !");
                location.reload();
            } else {
                alert("Erreur lors du minage.");
            }
        })
        .catch(error => console.error("Erreur lors de l'envoi des données de minage :", error));
    });
});
</script>
<?php include '../include/footer.php'; ?>