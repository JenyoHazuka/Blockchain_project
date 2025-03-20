<?php
require_once("connexion.php");

// Effectuer la requête pour récupérer les données des wallets
$stmt = $pdo->query("SELECT nom_wallet, publicKey_wallet, privateKey_wallet, amount_wallet FROM wallets;");
$walletTable = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si aucun résultat, retourner un tableau vide
if (!$walletTable) {
    $walletTable = [];
}

// Convertir les données en JSON pour être utilisées côté client
echo json_encode($walletTable, JSON_PRETTY_PRINT);
?>
