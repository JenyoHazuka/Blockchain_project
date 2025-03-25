<?php
require_once("connexion.php");

// Effectuer la requête pour récupérer les données des wallets
$stmt = $pdo->query("SELECT id_wallet, nom_wallet, amount_wallet, publicKey_wallet FROM wallets;"); 
$walletOpt = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si aucun résultat, retourner un tableau vide
if (!$walletOpt) {
    $walletOpt = [];
}

// Convertir les données en JSON pour être utilisées côté client
header('Content-Type: application/json'); // S'assurer que la réponse est en JSON
echo json_encode($walletOpt, JSON_PRETTY_PRINT);

// Traitement de la transaction si en méthode POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $sender = $data['sender'];
    $receiver = $data['receiver'];
    $amount = floatval($data['amount']);
    $fees = floatval($data['fees']);

    if ($sender == $receiver) {
        echo json_encode(["success" => false, "error" => "L'expéditeur et le receveur doivent être différents."]);
        exit;
    }

    $pdo->beginTransaction();
    try {
        // Récupérer les noms des wallets de l'expéditeur et du receveur
        $stmt = $pdo->prepare("SELECT nom_wallet FROM wallets WHERE id_wallet = ?");
        $stmt->execute([$sender]);
        $sender_name = $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT nom_wallet FROM wallets WHERE id_wallet = ?");
        $stmt->execute([$receiver]);
        $receiver_name = $stmt->fetchColumn();

        // Vérifier les fonds de l'expéditeur
        $stmt = $pdo->prepare("SELECT amount_wallet FROM wallets WHERE id_wallet = ?");
        $stmt->execute([$sender]);
        $sender_balance = $stmt->fetchColumn();

        if ($sender_balance < ($amount + $fees)) {
            echo json_encode(["success" => false, "error" => "Fonds insuffisants !"]);
            exit;
        }

        // Récupérer la clé publique de l'expéditeur
        $stmt = $pdo->prepare("SELECT publicKey_wallet FROM wallets WHERE id_wallet = ?");
        $stmt->execute([$sender]);
        $publicKey_sender = $stmt->fetchColumn();

        // Débiter l'expéditeur
        $stmt = $pdo->prepare("UPDATE wallets SET amount_wallet = amount_wallet - ? WHERE id_wallet = ?");
        $stmt->execute([$amount + $fees, $sender]);

        // Créditer le receveur
        $stmt = $pdo->prepare("UPDATE wallets SET amount_wallet = amount_wallet + ? WHERE id_wallet = ?");
        $stmt->execute([$amount, $receiver]);

        // Ajouter la transaction dans la table "transactions" avec les noms
        $stmt = $pdo->prepare("INSERT INTO transactions (expediteur_transaction, destinataire_transaction, montant_transaction, frais_transaction, publicKey_expediteur, statut) 
                               VALUES (?, ?, ?, ?, ?, 'En Attente')");
        $stmt->execute([$sender_name, $receiver_name, $amount, $fees, $publicKey_sender]);

        // Valider la transaction
        $pdo->commit();
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["success" => false, "error" => "Erreur : " . $e->getMessage()]);
    }
}
?>
