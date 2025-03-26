<?php include '../include/header.php'; ?>
<main>
    <div class="container">
        <h1 class="title-block">Recherche de Bloc</h1>
        <div class="search-container">
            <input type="text" placeholder="Recherche par ID de bloc..." id="search-input-id" class="search-bar">
            <input type="text" placeholder="Recherche par Hash de bloc..." id="search-input-hash" class="search-bar">
        </div>
        <div id="listBlocks" class="list-Blocks">
            <!-- Insertion JS -->
        </div>
    </div>
</main>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInputId = document.querySelector("#search-input-id");
        const searchInputHash = document.querySelector("#search-input-hash");
        const container = document.querySelector("#listBlocks");
        let blocksData = [];

        function renderBlocks(filteredData) {
            container.innerHTML = "";
            if (filteredData.length === 0) {
                container.innerHTML = "<p>Aucun bloc ne correspond</p>";
                return;
            }
            filteredData.forEach(bloc => {
                let divBlock = document.createElement("div");
                divBlock.classList.add("block");

                let divId = document.createElement("div");
                divId.classList.add("block-num");
                divId.innerHTML = `<strong>Bloc #${bloc.num_block}</strong>`;
                divBlock.appendChild(divId);

                let divHashBlock = document.createElement("div");
                divHashBlock.classList.add("block-hash");
                divHashBlock.innerHTML = `<strong>Hash : </strong>${bloc.hash_block}`;
                divBlock.appendChild(divHashBlock);

                let divHashPrec = document.createElement("div");
                divHashPrec.classList.add("block-hash-prec");
                divHashPrec.innerHTML = `<strong>Hash Précédent : </strong>${bloc.hash_prec}`;
                divBlock.appendChild(divHashPrec);

                let divVersion = document.createElement("div");
                divVersion.classList.add("block-version");
                divVersion.innerHTML = `<strong>Version : </strong>${bloc.version}`;
                divBlock.appendChild(divVersion);

                let divTarget = document.createElement("div");
                divTarget.classList.add("block-target");
                divTarget.innerHTML = `<strong>Target : </strong>${bloc.target}`;
                divBlock.appendChild(divTarget);

                let divNonce = document.createElement("div");
                divNonce.classList.add("block-nonce");
                divNonce.innerHTML = `<strong>Nonce : </strong>${bloc.nonce}`;
                divBlock.appendChild(divNonce);

                let divMerkleRoot = document.createElement("div");
                divMerkleRoot.classList.add("block-merkle-root");
                divMerkleRoot.innerHTML = `<strong>Hash Merkle Root : </strong>${bloc.merkle_root}`;
                divBlock.appendChild(divMerkleRoot);

                let divTimestamps = document.createElement("div");
                divTimestamps.classList.add("block-timestamps");
                divTimestamps.innerHTML = `<strong>Timestamps : </strong>${bloc.timestamps}`;
                divBlock.appendChild(divTimestamps);

                // Ajouter le bloc à la liste
                container.appendChild(divBlock);
            });
        }

        // Ajouter un événement d'écoute sur les deux champs de recherche
        function filterBlocks() {
            const searchValueId = searchInputId.value.toLowerCase();
            const searchValueHash = searchInputHash.value.toLowerCase();

            // Si la recherche est vide dans les deux champs, récupérer tous les blocs
            if (searchValueId.trim() === "" && searchValueHash.trim() === "") {
                fetchBlocks();
            } else {
                fetch(`../src/search.php?q_id=${encodeURIComponent(searchValueId)}&q_hash=${encodeURIComponent(searchValueHash)}`)
                    .then(response => response.json())
                    .then(data => {
                        blocksData = data;
                        renderBlocks(blocksData);
                    });
            }
        }

        // Écouter les événements d'input sur les deux champs
        searchInputId.addEventListener("input", filterBlocks);
        searchInputHash.addEventListener("input", filterBlocks);

        function fetchBlocks() {
            fetch("../src/blocks-be.php")
                .then(response => response.json())
                .then(data => {
                    blocksData = data;
                    renderBlocks(blocksData);
                });
        }

        // Charger les blocs au départ
        fetchBlocks();
    });
</script>
<?php include '../include/footer.php'; ?>
