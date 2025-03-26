<?php
require_once("connexion.php");

// Effectuer la requête pour récupérer les données des blocks
$stmt = $pdo->query("SELECT * FROM blocks");
$blockTable = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si aucun résultat, retourner un tableau vide
if (!$blockTable) {
    $blockTable = [];
}

// Convertir les données en JSON pour être utilisées côté client
echo json_encode($blockTable, JSON_PRETTY_PRINT);
?>