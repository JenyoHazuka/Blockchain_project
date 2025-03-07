let publicKey, privateKey, signedMessage;
let encoder = new TextEncoder();
let decoder = new TextDecoder();

function Hashage() {
    const message = document.getElementById("message-hash").value;
    const md = forge.md.sha256.create();
    md.update(message);
    document.getElementById("hashedMessage").textContent = md.digest().toHex();
}

// Fonction pour générer les clés ECDSA (clé publique et clé privée)
function generateKeys() {
    const ecdsaAlgorithm = {
        name: "ECDSA",
        namedCurve: "P-256" // Utilisation de la courbe elliptique P-256
    };

    window.crypto.subtle.generateKey(ecdsaAlgorithm, true, ["sign", "verify"]).then(function(keyPair) {
        publicKey = keyPair.publicKey;
        privateKey = keyPair.privateKey;

        // Convertir les clés en PEM et afficher
        displayKeys();
    }).catch(function(err) {
        console.error("Erreur lors de la génération des clés ECDSA : ", err);
    });
}

// Fonction pour convertir une clé en format PEM (public et privée) sans ArrayBuffer
function keyToPEM(key, type) {
    return window.crypto.subtle.exportKey(type, key).then(function(exportedKey) {
        const keyArray = new Uint8Array(exportedKey);
        let keyString = String.fromCharCode.apply(null, keyArray);
        return btoa(keyString)
    });
}

// Fonction pour afficher les clés en PEM
function displayKeys() {
    // Affichage de la clé publique dans les éléments disponibles
    keyToPEM(publicKey, "spki").then(function(pemPublicKey) {
        // Vérifier si les éléments existent avant de mettre à jour
        if (document.getElementById("publicKey")) {
            document.getElementById("publicKey").textContent = pemPublicKey;
        }
        if (document.getElementById("wallet-public-key")) {
            document.getElementById("wallet-public-key").value = pemPublicKey;
        }
    });

    // Affichage de la clé privée dans les éléments disponibles
    keyToPEM(privateKey, "pkcs8").then(function(pemPrivateKey) {
        if (document.getElementById("privateKey")) {
            document.getElementById("privateKey").textContent = pemPrivateKey;
        }
        if (document.getElementById("wallet-private-key")) {
            document.getElementById("wallet-private-key").value = pemPrivateKey;
        }
    });
}

// Fonction pour signer un message avec la clé privée
function encryptMessage() {
    const message = document.getElementById("message-crypt").value;
    const encoder = new TextEncoder();
    const encodedMessage = encoder.encode(message);

    window.crypto.subtle.sign(
        { name: "ECDSA", hash: { name: "SHA-256" } }, // Signer avec ECDSA et SHA-256
        privateKey,  // Clé privée
        encodedMessage  // Message à signer
    ).then(function(signature) {
        signedMessage = btoa(String.fromCharCode(...new Uint8Array(signature))); // Convertir la signature en Base64
        document.getElementById("signedMessage").textContent = signedMessage; // Afficher la signature
    }).catch(function(err) {
        console.error("Erreur lors de la signature du message : ", err);
    });
}

// Fonction pour vérifier la signature d'un message avec la clé publique
function decryptMessage() {
    const message = document.getElementById("message-crypt").value;

    if (!signedMessage) {
        document.getElementById("decryptedMessage").textContent = "Aucune signature à vérifier.";
        return;
    }

    const encoder = new TextEncoder();
    const encodedMessage = encoder.encode(message);
    const signature = new Uint8Array(atob(signedMessage).split("").map(c => c.charCodeAt(0))); // Convertir la signature Base64 en Uint8Array

    window.crypto.subtle.verify(
        { name: "ECDSA", hash: { name: "SHA-256" } }, // Vérification avec ECDSA et SHA-256
        publicKey, // Clé publique
        signature, // Signature à vérifier
        encodedMessage  // Message signé à vérifier
    ).then(function(isValid) {
        if (isValid) {
            document.getElementById("decryptedMessage").textContent = message;
        } else {
            document.getElementById("decryptedMessage").textContent = "Le message n'est pas valide!";
        }
    }).catch(function(err) {
        console.error("Erreur lors de la vérification de la signature : ", err);
    });
}

/* Code Wallet - n'est exécuté que si les éléments existent */
document.addEventListener('DOMContentLoaded', function() {
    // Gestionnaires d'événements pour la page wallet uniquement
    if (document.getElementById('generate-keys')) {
        document.getElementById('generate-keys').addEventListener('click', generateKeys);
    }
    
    if (document.getElementById('copy-public-key')) {
        document.getElementById('copy-public-key').addEventListener('click', function() {
            copyToClipboard('wallet-public-key');
        });
    }
    
    if (document.getElementById('copy-private-key')) {
        document.getElementById('copy-private-key').addEventListener('click', function() {
            copyToClipboard('wallet-private-key');
        });
    }
    
    // Gestionnaire pour le formulaire de wallet
    if (document.getElementById('wallet-form')) {
        document.getElementById('wallet-form').addEventListener('submit', saveWallet);
    }
    
    // Vérifier si un ID est déjà saisi et charger les données (page wallet uniquement)
    if (document.getElementById('wallet-id')) {
        document.getElementById('wallet-id').addEventListener('blur', checkWalletId);
    }
    
    // Gestionnaires pour d'autres pages avec crypto/signature
    if (document.getElementById('btn-hash') && document.getElementById('message-hash')) {
        document.getElementById('btn-hash').addEventListener('click', Hashage);
    }
    
    if (document.getElementById('btn-encrypt') && document.getElementById('message-crypt')) {
        document.getElementById('btn-encrypt').addEventListener('click', encryptMessage);
    }
    
    if (document.getElementById('btn-decrypt') && document.getElementById('message-crypt')) {
        document.getElementById('btn-decrypt').addEventListener('click', decryptMessage);
    }
});

// Fonction pour copier dans le presse-papier
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    element.select();
    document.execCommand('copy');
    
    // Indiquer visuellement que la copie a été effectuée
    const originalText = element.parentElement.querySelector('.btn-copy').textContent;
    element.parentElement.querySelector('.btn-copy').textContent = 'Copié !';
    setTimeout(() => {
        element.parentElement.querySelector('.btn-copy').textContent = originalText;
    }, 1500);
}

// Vérifier si un wallet avec cet ID existe déjà
function checkWalletId() {
    const walletId = document.getElementById('wallet-id').value.trim();
    
    if (walletId === '') {
        return;
    }
    
    fetch(`../src/wallet-be.php?action=check_wallet&id=${encodeURIComponent(walletId)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.exists) {
                // Charger les données du wallet existant
                loadWalletData(walletId);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
}

// Charger les données d'un wallet existant
function loadWalletData(walletId) {
    fetch(`../src/wallet-be.php?action=get_wallet&id=${encodeURIComponent(walletId)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remplir le formulaire avec les données du wallet
                document.getElementById('wallet-public-key').value = data.wallet.public_key;
                
                // Effacer l'affichage des clés dans les éléments <pre> s'ils existent
                if (document.getElementById('privateKey')) {
                    document.getElementById('privateKey').textContent = "";
                }
                if (document.getElementById('publicKey')) {
                    document.getElementById('publicKey').textContent = "";
                }
                
                document.getElementById('wallet-private-key').value = data.wallet.private_key;
                document.getElementById('balance-amount').textContent = data.wallet.balance.toFixed(8);
                
                showNotification('Wallet existant chargé', 'success');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('Erreur lors du chargement du wallet', 'error');
        });
}

// Sauvegarder le wallet
function saveWallet(event) {
    event.preventDefault();
    
    const walletId = document.getElementById('wallet-id').value.trim();
    const publicKey = document.getElementById('wallet-public-key').value.trim();
    const privateKey = document.getElementById('wallet-private-key').value.trim();
    const balance = parseFloat(document.getElementById('balance-amount').textContent) || 0;
    
    // Vérifier que toutes les informations nécessaires sont présentes
    if (!walletId || !publicKey || !privateKey) {
        showNotification('Veuillez remplir tous les champs et générer des clés', 'error');
        return;
    }
    
    // Créer un objet FormData pour l'envoi
    const formData = new FormData();
    formData.append('id', walletId);
    formData.append('public_key', publicKey);
    formData.append('private_key', privateKey);
    formData.append('balance', balance);
    
    // Appel à l'API
    fetch('../src/wallet-be.php?action=save_wallet', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Wallet sauvegardé avec succès', 'success');
        } else {
            showNotification(data.message || 'Erreur lors de la sauvegarde', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur de connexion au serveur', 'error');
    });
}

// Afficher une notification
function showNotification(message, type) {
    // Supprimer les notifications existantes
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => {
        notification.remove();
    });
    
    // Créer une nouvelle notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Ajouter la notification au document
    document.body.appendChild(notification);
    
    // Afficher la notification avec une animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Supprimer la notification après un délai
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}