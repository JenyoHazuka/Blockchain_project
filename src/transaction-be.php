<?php
require_once ('connexion.php');

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['action']) && $_GET['action'] === "get_wallets") {
    $stmt = $pdo->query("SELECT id_wallet, nom_wallet, amount_wallet FROM wallets WHERE amount_wallet > 0");
    echo json_encode($stmt->fetchAll());
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sender = $_POST['sender'];
    $receiver = $_POST['receiver'];
    $amount = floatval($_POST['amount']);
    $fees = floatval($_POST['fees']);

    if ($sender == $receiver) {
        die("L'expéditeur et le receveur doivent être différents.");
    }

    $pdo->beginTransaction();
    try {
        // Vérifier les fonds de l'expéditeur
        $stmt = $pdo->prepare("SELECT amount_wallet FROM wallets WHERE id_wallet = ?");
        $stmt->execute([$sender]);
        $sender_balance = $stmt->fetchColumn();

        if ($sender_balance < ($amount + $fees)) {
            die("Fonds insuffisants !");
        }

        // Débiter l'expéditeur
        $stmt = $pdo->prepare("UPDATE wallets SET amount_wallet = amount_wallet - ? WHERE id_wallet = ?");
        $stmt->execute([$amount + $fees, $sender]);

        // Créditer le receveur
        $stmt = $pdo->prepare("UPDATE wallets SET amount_wallet = amount_wallet + ? WHERE id_wallet = ?");
        $stmt->execute([$amount, $receiver]);

        // Valider la transaction
        $pdo->commit();
        echo "Transaction réussie !";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Erreur : " . $e->getMessage();
    }
}
?>
