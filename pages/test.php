<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau Paginer</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f4f4f4; }
        .pagination { text-align: center; margin-top: 10px; }
        .pagination button { padding: 5px 10px; margin: 2px; border: none; cursor: pointer; background: #007bff; color: white; }
        .pagination button:disabled { background: #ccc; cursor: not-allowed; }
    </style>
</head>
<body>

<?php
// Simulons des données (vous pouvez remplacer cela par une requête SQL)
$data = [];
for ($i = 1; $i <= 25; $i++) {
    $data[] = ["id" => $i, "nom" => "Produit $i", "prix" => rand(10, 100) . " €"];
}

// Envoi des données en JSON pour JavaScript
echo "<script>let tableData = " . json_encode($data) . ";</script>";
?>

<h2>Tableau Paginer</h2>
<table id="dataTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prix</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<div class="pagination">
    <button id="prevPage" disabled>Précédent</button>
    <span id="pageInfo"></span>
    <button id="nextPage">Suivant</button>
</div>

<script>
    let currentPage = 1;
    const rowsPerPage = 10;
    
    function displayTable(page) {
        let tbody = document.querySelector("#dataTable tbody");
        tbody.innerHTML = "";
        let start = (page - 1) * rowsPerPage;
        let end = start + rowsPerPage;
        let paginatedItems = tableData.slice(start, end);

        paginatedItems.forEach(item => {
            let row = `<tr>
                <td>${item.id}</td>
                <td>${item.nom}</td>
                <td>${item.prix}</td>
            </tr>`;
            tbody.innerHTML += row;
        });

        document.getElementById("pageInfo").textContent = `Page ${page} sur ${Math.ceil(tableData.length / rowsPerPage)}`;
        document.getElementById("prevPage").disabled = page === 1;
        document.getElementById("nextPage").disabled = end >= tableData.length;
    }

    document.getElementById("prevPage").addEventListener("click", () => {
        if (currentPage > 1) {
            currentPage--;
            displayTable(currentPage);
        }
    });

    document.getElementById("nextPage").addEventListener("click", () => {
        if (currentPage * rowsPerPage < tableData.length) {
            currentPage++;
            displayTable(currentPage);
        }
    });

    displayTable(currentPage);
</script>

</body>
</html>
