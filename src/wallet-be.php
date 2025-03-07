<?php
// Définir l'en-tête pour JSON
header('Content-Type: application/json');

// Configuration de la base de données
$dbFile = __DIR__ . '/../data/wallets.json';

// Fonction d'initialisation de la base de données si elle n'existe pas
function initDatabase() {
    global $dbFile;
    
    $dir = dirname($dbFile);
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
    
    if (!file_exists($dbFile)) {
        file_put_contents($dbFile, json_encode([]));
    }
}

// Fonction pour lire tous les wallets
function readWallets() {
    global $dbFile;
    
    initDatabase();
    $content = file_get_contents($dbFile);
    return json_decode($content, true) ?: [];
}

// Fonction pour écrire tous les wallets
function writeWallets($wallets) {
    global $dbFile;
    
    initDatabase();
    file_put_contents($dbFile, json_encode($wallets, JSON_PRETTY_PRINT));
}

// Fonction pour obtenir un wallet spécifique
function getWallet($id) {
    $wallets = readWallets();
    return isset($wallets[$id]) ? $wallets[$id] : null;
}

// Fonction pour sauvegarder un wallet
function saveWallet($id, $publicKey, $privateKey, $balance = 0) {
    $wallets = readWallets();
    
    $wallets[$id] = [
        'id' => $id,
        'public_key' => $publicKey,
        'private_key' => $privateKey,
        'balance' => (float)$balance,
        'created_at' => isset($wallets[$id]['created_at']) ? $wallets[$id]['created_at'] : date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    writeWallets($wallets);
    return true;
}

// Traitement des actions
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'check_wallet':
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $exists = getWallet($id) !== null;
        
        echo json_encode([
            'success' => true,
            'exists' => $exists
        ]);
        break;
        
    case 'get_wallet':
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $wallet = getWallet($id);
        
        if ($wallet) {
            echo json_encode([
                'success' => true,
                'wallet' => $wallet
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Wallet non trouvé'
            ]);
        }
        break;
        
    case 'save_wallet':
        $id = isset($_POST['id']) ? $_POST['id'] : '';
        $publicKey = isset($_POST['public_key']) ? $_POST['public_key'] : '';
        $privateKey = isset($_POST['private_key']) ? $_POST['private_key'] : '';
        $balance = isset($_POST['balance']) ? (float)$_POST['balance'] : 0;
        
        if (empty($id) || empty($publicKey) || empty($privateKey)) {
            echo json_encode([
                'success' => false,
                'message' => 'Tous les champs sont obligatoires'
            ]);
            break;
        }
        
        saveWallet($id, $publicKey, $privateKey, $balance);
        
        echo json_encode([
            'success' => true,
            'message' => 'Wallet sauvegardé avec succès'
        ]);
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Action non reconnue'
        ]);
        break;
}