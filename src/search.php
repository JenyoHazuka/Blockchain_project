<?php
require_once('connexion.php');

// Récupérer les termes de recherche
$searchId = isset($_GET['q_id']) ? $_GET['q_id'] : '';
$searchHash = isset($_GET['q_hash']) ? $_GET['q_hash'] : '';

// Construire la requête SQL
$sql = "SELECT * FROM blocks WHERE 1=1";

if ($searchId) {
    // Si un terme de recherche sur l'ID est fourni
    $sql .= " AND num_block LIKE :searchId";
}

if ($searchHash) {
    // Si un terme de recherche sur le hash est fourni
    $sql .= " AND hash_block LIKE :searchHash";
}

// Préparer et exécuter la requête
$stmt = $pdo->prepare($sql);

if ($searchId) {
    $stmt->bindValue(':searchId', '%' . $searchId . '%');
}

if ($searchHash) {
    $stmt->bindValue(':searchHash', '%' . $searchHash . '%');
}

$stmt->execute();

// Récupérer les résultats sous forme de tableau
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retourner les résultats en format JSON
echo json_encode($results);
?>
