/* Code Hash & Cryptage */
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

    window.crypto.subtle.generateKey(ecdsaAlgorithm, true, ["sign", "verify"]).then(function (keyPair) {
        publicKey = keyPair.publicKey;
        privateKey = keyPair.privateKey;

        // Convertir les clés en PEM et afficher
        displayKeys();
    }).catch(function (err) {
        console.error("Erreur lors de la génération des clés ECDSA : ", err);
    });
}

// Fonction pour convertir une clé en format PEM (public et privée) sans ArrayBuffer
function keyToPEM(key, type) {
    return window.crypto.subtle.exportKey(type, key).then(function (exportedKey) {
        const keyArray = new Uint8Array(exportedKey);
        let keyString = String.fromCharCode.apply(null, keyArray);
        return btoa(keyString)
    });
}

// Fonction pour afficher les clés en PEM
function displayKeys() {
    // Affichage de la clé publique dans les éléments disponibles
    keyToPEM(publicKey, "spki").then(function (pemPublicKey) {
        // Vérifier si les éléments existent avant de mettre à jour
        if (document.getElementById("publicKey")) {
            document.getElementById("publicKey").textContent = pemPublicKey;
        }
        if (document.getElementById("wallet-public-key")) {
            document.getElementById("wallet-public-key").value = pemPublicKey;
        }
    });

    // Affichage de la clé privée dans les éléments disponibles
    keyToPEM(privateKey, "pkcs8").then(function (pemPrivateKey) {
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
    ).then(function (signature) {
        signedMessage = btoa(String.fromCharCode(...new Uint8Array(signature))); // Convertir la signature en Base64
        document.getElementById("signedMessage").textContent = signedMessage; // Afficher la signature
    }).catch(function (err) {
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
    ).then(function (isValid) {
        if (isValid) {
            document.getElementById("decryptedMessage").textContent = message;
        } else {
            document.getElementById("decryptedMessage").textContent = "Le message n'est pas valide!";
        }
    }).catch(function (err) {
        console.error("Erreur lors de la vérification de la signature : ", err);
    });
}

/* Code Wallet */
document.addEventListener("DOMContentLoaded", function () {
    const generateKeysBtn = document.getElementById("generate-keys");
    const walletForm = document.getElementById("walletForm");
    const submitBtn = walletForm.querySelector('button[type="submit"]');

    let publicKey, privateKey;

    // Fonction pour générer les clés
    async function generateKeys() {
        try {
            // Désactiver le bouton de soumission tant que les clés ne sont pas générées
            submitBtn.disabled = true;

            // Génération des clés ECDSA
            const keyPair = await window.crypto.subtle.generateKey(
                {
                    name: "ECDSA",
                    namedCurve: "P-256"
                },
                true,
                ["sign", "verify"]
            );

            // Assigner les clés générées à des variables
            publicKey = keyPair.publicKey;
            privateKey = keyPair.privateKey;

            // Convertir les clés en format PEM (Base64)
            const publicKeyPEM = await keyToPEM(publicKey, "spki");
            const privateKeyPEM = await keyToPEM(privateKey, "pkcs8");

            // Afficher les clés dans les champs de texte du formulaire
            document.getElementById("wallet-public-key").value = publicKeyPEM;
            document.getElementById("wallet-private-key").value = privateKeyPEM;

            // Réactiver le bouton de soumission après génération des clés
            submitBtn.disabled = false;
        } catch (error) {
            console.error("Erreur lors de la génération des clés :", error);
        }
    }

    // Fonction pour convertir une clé en format PEM
    async function keyToPEM(key, type) {
        const exportedKey = await window.crypto.subtle.exportKey(type, key);
        return btoa(String.fromCharCode(...new Uint8Array(exportedKey)));
    }

    // Ajouter l'événement sur le bouton de génération des clés
    generateKeysBtn.addEventListener("click", generateKeys);

    // Gestion de la soumission du formulaire
    walletForm.addEventListener("submit", async function (event) {
        event.preventDefault();  // Empêche la soumission classique du formulaire

        const walletId = document.getElementById("wallet-id").value.trim();
        const publicKey = document.getElementById("wallet-public-key").value.trim();
        const privateKey = document.getElementById("wallet-private-key").value.trim();
        const balance = "0.00000000";  // Solde par défaut

        // Validation des champs
        if (!walletId || !publicKey || !privateKey) {
            alert("Veuillez remplir tous les champs et générer des clés.");
            return;
        }

        // Créer l'objet FormData
        const formData = new FormData();
        formData.append("walletName", walletId);
        formData.append("publicKey", publicKey);
        formData.append("privateKey", privateKey);
        formData.append("balance", balance);

        try {
            const response = await fetch("../src/wallet-be.php", {
                method: "POST",
                body: formData  // Envoi des données sous forme de FormData
            });

            if (!response.ok) {
                throw new Error("Une erreur est survenue lors de l'envoi des données");
            }

            const result = await response.json();
            console.log(result);

            if (result.success) {
                alert("L'inscription a réussi");
            } else {
                alert("Erreur: " + result.message);
            }
        } catch (error) {
            console.error("Erreur lors de l'inscription :", error);
            alert("L'inscription a échoué");
        }
    });
});

/* Code Wallet List */
let currentPageWallet = 1;
const rowsPerPageWallet = 5;

// Fonction pour afficher le tableau avec la pagination
function displayTableWallet(pageWallet) {
    let tbody = document.querySelector("#dataTableWallets tbody");
    tbody.innerHTML = ""; // Vider le corps du tableau avant de réinsérer les lignes
    let start = (pageWallet - 1) * rowsPerPageWallet;
    let end = start + rowsPerPageWallet;
    let paginatedItems = window.walletTable.slice(start, end);

    if (paginatedItems.length === 0) {
        tbody.innerHTML = "<tr><td colspan='4'>Aucune donnée à afficher</td></tr>";
    } else {
        paginatedItems.forEach(item => {
            let row = `<tr>
                <td>${item.nom_wallet}</td>
                <td>${item.publicKey_wallet}</td>
                <td>${item.privateKey_wallet}</td>
                <td>${item.amount_wallet} BTC</td>
            </tr>`;
            tbody.innerHTML += row;
        });
    }

    document.getElementById("pageInfoWallet").textContent = `Page ${page} sur ${Math.ceil(window.walletTable.length / rowsPerPageWallet)}`;
    document.getElementById("prevPageWallet").disabled = page === 1;
    document.getElementById("nextPageWallet").disabled = end >= window.walletTable.length;
}

// Gestion du bouton "Précédent"
document.getElementById("prevPageWallet").addEventListener("click", () => {
    if (currentPageWallet > 1) {
        currentPageWallet--;
        displayTableWallet(currentPageWallet);
    }
});

// Gestion du bouton "Suivant"
document.getElementById("nextPageWallet").addEventListener("click", () => {
    if (currentPageWallet * rowsPerPageWallet < window.walletTable.length) {
        currentPageWallet++;
        displayTableWallet(currentPageWallet);
    }
});

/* Code Mempool */
let currentPageMempool = 1;
const rowsPerPageMempool = 5;

// Fonction pour afficher le tableau avec la pagination
function displayTableMempool(pageMempool) {
    let tbody = document.querySelector("#dataTableMempools tbody");
    tbody.innerHTML = ""; // Vider le corps du tableau avant de réinsérer les lignes
    let start = (pageMempool - 1) * rowsPerPageMempool;
    let end = start + rowsPerPageMempool;
    let paginatedItems = window.mempoolTable.slice(start, end);

    if (paginatedItems.length === 0) {
        tbody.innerHTML = "<tr><td colspan='4'>Aucune donnée à afficher</td></tr>";
    } else {
        paginatedItems.forEach(tx => {
            let row = `<tr>
                <td>${tx.expediteur_transaction}</td>
                <td>${tx.destinataire_transaction}</td>
                <td>${tx.montant_transaction} BTC</td>
                <td>${tx.frais_transaction}</td>
            </tr>`;
            tbody.innerHTML += row;
        });
    }

    document.getElementById("pageInfoMempool").textContent = `Page ${page} sur ${Math.ceil(window.mempoolTable.length / rowsPerPageMempool)}`;
    document.getElementById("prevPageMempool").disabled = page === 1;
    document.getElementById("nextPageMempool").disabled = end >= window.mempoolTable.length;
}

// Gestion du bouton "Précédent"
document.getElementById("prevPageMempool").addEventListener("click", () => {
    if (currentPageMempool > 1) {
        currentPageMempool--;
        displayTableMempool(currentPageMempool);
    }
});

// Gestion du bouton "Suivant"
document.getElementById("nextPageMempool").addEventListener("click", () => {
    if (currentPageMempool * rowsPerPageMempool < window.mempoolTable.length) {
        currentPageMempool++;
        displayTableMempool(currentPageMempool);
    }
});

/* Code SearchBar */

