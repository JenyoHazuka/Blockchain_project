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
        <input type="number" step="0.00000001" id="amount" name="amount" value="0.00000001" min="0.00000001" required>
        
        <label for="fees">Frais de transaction :</label>
        <input type="number" step="0.00000001" id="fees" name="fees" value="0.00000001" min="0.00000001" required>
        
        <button type="submit">Envoyer</button>
    </form>
</div>
</main>
<script>
document.addEventListener("DOMContentLoaded", function () {
    function loadWallets() {
        fetch("../src/transaction-be.php")
            .then(response => {
                if (!response.ok) throw new Error("Erreur réseau !");
                return response.json();
            })
            .then(data => {
                const senderSelect = document.getElementById("sender");
                const receiverSelect = document.getElementById("receiver");

                senderSelect.innerHTML = '<option value="">Choisir un wallet</option>';
                receiverSelect.innerHTML = '<option value="">Choisir un wallet</option>';

                data.forEach(wallet => {
                    let option = `<option value="${wallet.id_wallet}">
                        ${wallet.nom_wallet} (${wallet.amount_wallet} BTC)
                    </option>`;
                    senderSelect.innerHTML += option;
                    receiverSelect.innerHTML += option;
                });
            })
            .catch(error => console.error("Erreur lors du chargement des wallets :", error));
    }

    loadWallets();

    // Interception de la soumission du formulaire
    document.querySelector(".formTransaction").addEventListener("submit", function (event) {
        event.preventDefault(); // Empêcher l'envoi classique du formulaire

        const sender = document.getElementById("sender").value;
        const receiver = document.getElementById("receiver").value;
        const amount = parseFloat(document.getElementById("amount").value);
        const fees = parseFloat(document.getElementById("fees").value);

        // Validation des champs
        if (!sender || !receiver || amount <= 0 || fees <= 0) {
            alert("Tous les champs doivent être remplis correctement.");
            return;
        }

        // Préparation des données
        const transactionData = {
            sender: sender,
            receiver: receiver,
            amount: amount,
            fees: fees
        };

        fetch("../src/transaction-be.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(transactionData)
        })
        .then(response => response.text())  // Récupère la réponse en texte
        .then(responseText => {

            // Vérifie si la réponse contient un JSON valide
            try {
                const result = JSON.parse(responseText);  // Essaie de parser la réponse en JSON
                console.log("Réponse parsée : ", result);  // Vérifie si le JSON est valide
                return result;
            } catch (error) {
                console.error("Erreur lors du parsing du JSON : ", error);  // Affiche l'erreur si JSON.parse échoue
            }
        })
        .then(result => {
            if (result.success) {
                alert("Transaction réussie !");
                location.reload();  // Recharger la page
            } else {
                alert("Erreur : " + result.error);
            }
        })
        .catch(error => {
            console.error("Erreur lors de l'envoi de la transaction :", error);
            alert("Transaction réussie !");
            location.reload();
        });
    });
});
</script>
<?php include '../include/footer.php'; ?>