<?php include '../include/header.php'; ?>

<div class="wallet-container">
    <h1>Bitcoin Wallet</h1>
    
    <div class="wallet-form">
        <h2>Créer votre wallet</h2>
        <form id="wallet-form">
            <div class="form-group">
                <label for="wallet-id">Nom du wallet:</label>
                <input type="text" id="wallet-id" required placeholder="Entrez un nom unique">
            </div>
            <div class="form-group">
                <button type="button" id="generate-keys" class="btn btn-secondary">Générer les clés</button>
            </div>
            <div class="form-group">
                <label for="wallet-public-key">Clé publique:</label>
                <div class="key-container">
                    <input type="text" id="wallet-public-key" readonly>
                </div>
            </div>
            <div class="form-group">
                <label for="wallet-private-key">Clé privée:</label>
                <div class="key-container">
                    <input type="text" id="wallet-private-key" readonly>
                </div>
            </div>
            <div class="wallet-balance">
                <h3>Solde</h3>
                <div class="balance">
                    <span id="balance-amount">0.00000000</span> BTC
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Sauvegarder</button>
            </div>
        </form>
    </div>
</div>

<?php include '../include/footer.php'; ?>