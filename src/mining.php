<?php
include 'src/connexion.php';
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["wallet"], $data["hash_prec"], $data["version"], $data["target"], $data["transactions"])) {
    echo json_encode(["success" => false, "error" => "Données invalides"]);
    exit;
}

$wallet = $data["wallet"];
$hash_prec = $data["hash_prec"];
$version = $data["version"];
$target = (int) $data["target"];
$transactions = $data["transactions"];

// Génération du Merkle Root (SHA-256 des transactions sélectionnées)
$tx_hashes = [];
foreach ($transactions as $tx_id) {
    $tx_data = $pdo->prepare("SELECT id_transaction, expediteur_transaction, destinataire_transaction, montant_transaction, frais_transaction FROM transactions WHERE id_transaction = ?");
    $tx_data->execute([$tx_id]);
    $tx = $tx_data->fetch(PDO::FETCH_ASSOC);
    if ($tx) {
        $tx_string = implode("|", $tx);
        $tx_hashes[] = hash("sha256", $tx_string);
    }
}
$merkle_root = hash("sha256", implode("", $tx_hashes));

// Minage : incrémentation du nonce jusqu'à obtenir un hash valide
$nonce = 0;
$hash_block = "";
$prefix = str_repeat("0", $target);

do {
    $block_data = $hash_prec . $version . $target . $nonce . $merkle_root;
    $hash_block = hash("sha256", $block_data);
    $nonce++;
} while (substr($hash_block, 0, $target) !== $prefix);

// Insertion du bloc miné dans la BDD
$stmt = $pdo->prepare("
    INSERT INTO blocks (hash_block, hash_prec, version, target, nonce, merkle_root) 
    VALUES (?, ?, ?, ?, ?, ?)
");
$success = $stmt->execute([$hash_block, $hash_prec, $version, $target, $nonce, $merkle_root]);

if ($success) {
    echo json_encode(["success" => true, "message" => "Block ajouté en base de données avec succès"]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout en base"]);
}
?>
