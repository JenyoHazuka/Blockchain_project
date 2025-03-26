<?php include '../include/header.php'; ?>

<main>
    <div class="container-formulaire-hashCrypt">
        <div class="formulaire">
            <div class="div-hash">
                <div class="title">
                    <h1>Hashage de message en SHA-256</h1>
                </div>
                <div class="message-hasher">
                    <textarea id="message-hash" placeholder="Entrez votre message..."></textarea>
                </div>
                <div class="button-hash">
                    <button onclick="Hashage()">Hasher</button>
                </div>
                <div class="result-hash">
                    <h3>Message en SHA-256 : </h3>
                    <pre id="hashedMessage"></pre>
                </div>
            </div>
        </div>
        <div class="separe"></div>
        <div class="formulaire">
            <div class="div-crypt">
                <div class="title">
                    <h1>Cryptage de message avec clé publique et clé privée</h1>
                </div>
                <div class="block-crypt">
                    <div class="form-group">
                        <div class="keys">
                            <div class="btn-keys">
                                <button onclick="generateKeys()">Générer les clés public et privée</button>
                            </div>
                            <h3>Clé Publique :</h3>
                            <pre id="publicKey"></pre>
                            <h3>Clé Privée :</h3>
                            <pre id="privateKey"></pre>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="message-crypts">
                            <textarea id="message-crypt" placeholder="Entrez votre message..."></textarea>
                        </div>
                        <div class="button-crypts">
                            <button onclick="encryptMessage()">Signer le message</button>
                            <button onclick="decryptMessage()">Vérifier la signature</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="result-crypt">
                            <h3>Message Signé :</h3>
                            <pre id="signedMessage"></pre>
                        </div>
                        <div class="result-decrypt">
                            <h3>Message Déchiffré (Si valide) :</h3>
                            <pre id="decryptedMessage"></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../include/footer.php'; ?>