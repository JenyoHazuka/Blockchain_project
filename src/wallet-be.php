<?php
header('Content-Type: application/json');
require_once("connexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Vérifier que les données arrivent
    // Liste des champs à vérifier
    $requiredFields = ["walletName", "publicKey", "privateKey"];
    $missingFields = [];

    // Vérification de chaque champ
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $missingFields[] = $field;
        }
    }

    // Si des champs sont manquants, renvoyer l'erreur
    if ($missingFields) {
        echo json_encode([
            "success" => false,
            "message" => "Données manquantes pour : " . implode(", ", $missingFields)
        ]);
        exit;
    }

    // Récupérer les données
    $walletName = trim($_POST["walletName"]);
    $publicKey = trim($_POST["publicKey"]);
    $privateKey = trim($_POST["privateKey"]);
    $balance = "0.00000000";  // Solde par défaut

    // Vérification si le wallet existe déjà
    $stmt = $pdo->prepare("SELECT id_wallet FROM wallets WHERE nom_wallet = ?");
    $stmt->execute([$walletName]);
    if ($stmt->fetch()) {
        echo json_encode(["success" => false, "message" => "Ce nom de wallet existe déjà."]);
        exit;
    }

    // Insérer le wallet dans la base de données
    $stmt = $pdo->prepare("INSERT INTO wallets (nom_wallet, publicKey_wallet, privateKey_wallet, amount_wallet) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$walletName, $publicKey, $privateKey, $balance])) {
        echo json_encode(["success" => true, "message" => "Inscription réussie !"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erreur lors de l'inscription."]);
    }
}
?>
