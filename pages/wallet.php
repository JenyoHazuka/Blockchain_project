<?php include '../include/header.php'; ?>

<main>
<div class="container">
    <h2>Inscription Wallet</h2>
    <button id="generate-keys">Générer les clés</button>
    <form id="walletForm">
        <div class="form-group">
            <label for="wallet-id">Nom du Wallet :</label>
            <input type="text" id="wallet-id" name="walletName" required placeholder="Entrez un nom unique">
        </div>
        
        <div class="form-group">
            <label for="wallet-public-key">Clé publique :</label>
            <input type="text" id="wallet-public-key" name="publicKey" readonly>
            <span id="wallet-public-key-display" hidden></span>
        </div>
        
        <div class="form-group">
            <label for="wallet-private-key">Clé privée :</label>
            <input type="text" id="wallet-private-key" name="privateKey" readonly>
            <span id="wallet-private-key-display" hidden></span>
        </div>
        
        <div class="form-group">
            <label for="balance">Solde (BTC) :</label>
            <input type="text" id="balance-amount" name="balance" value="0.00000000 BTC" readonly>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </div>
    </form>
</div>
</main>

<?php include '../include/footer.php'; ?>
