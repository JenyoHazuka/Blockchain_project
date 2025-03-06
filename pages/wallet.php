<?php include '../include/header.php'; ?>

<div class="container">
    <h2>Créer un Wallet Bitcoin</h2>
    <button onclick="generateWallet()">Générer un Wallet</button>
    <table>
        <thead>
            <tr>
                <th>Num</th>
                <th>Adresse</th>
                <th>Public Key</th>
                <th>Clé Privée</th>
                <th>Wallet</th>
            </tr>
        </thead>
        <tbody id="walletTableBody">
        </tbody>
    </table>
</div>

<?php include '../include/footer.php'; ?>