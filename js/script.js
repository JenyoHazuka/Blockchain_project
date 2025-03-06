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
    // Affichage de la clé publique
    keyToPEM(publicKey, "spki").then(function(pemPublicKey) {
        document.getElementById("publicKey").textContent = pemPublicKey;
    });

    // Affichage de la clé privée
    keyToPEM(privateKey, "pkcs8").then(function(pemPrivateKey) {
        document.getElementById("privateKey").textContent = pemPrivateKey;
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
