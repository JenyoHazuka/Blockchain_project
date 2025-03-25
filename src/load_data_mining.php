<?php
require_once('connexion.php');

$wallets = $pdo->query("SELECT nom_wallet FROM wallets")->fetchAll(PDO::FETCH_ASSOC);
$transactions = $pdo->query("
    SELECT id_transaction, expediteur_transaction, destinataire_transaction, montant_transaction, frais_transaction 
    FROM transactions
    WHERE statut = 'En Attente' 
    ORDER BY frais_transaction DESC
")->fetchAll(PDO::FETCH_ASSOC);

$hash_prec = $pdo->query("SELECT hash_block FROM blocks ORDER BY num_block DESC LIMIT 1")->fetchColumn() ?: str_repeat('0', 64);

echo json_encode([
    "wallets" => $wallets, 
    "transactions" => $transactions, 
    "hash_prec" => $hash_prec
]);
?>
