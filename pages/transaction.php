<?php include '../include/header.php'; ?>
<main>
<div class="container">
    <h2>Effectuer une transaction</h2>
    <form action="../src/transaction-be.php" method="POST" class="formTransaction">
        <label for="sender">De :</label>
        <select id="sender" name="sender" required>
            <option value="Choisir un wallet"></option>
        </select>
        
        <label for="receiver">Vers :</label>
        <select id="receiver" name="receiver" required>
            <option value="Choisir un wallet"></option>
        </select>

        <label for="amount">Montant :</label>
        <input type="number" step="0.00000001" id="amount" name="amount" required>
        
        <label for="fees">Frais de transaction :</label>
        <input type="number" step="0.00000001" id="fees" name="fees" required>
        
        <button type="submit">Envoyer</button>
    </form>
</div>
</main>
<?php include '../include/footer.php'; ?>