<div id="products">
    <h1>Produits</h1>

    <div class="table-container">

        <?php
        // Récupère la catégorie dans l'URL
        $selectedCategory = $_GET["category"] ?? null;

        $categories = [];

        //Ajout de la catégorie que si elle n'est pas présente
        foreach ($data as $product) {
            if (!in_array($product["category"], $categories)) {
                $categories[] = $product["category"];
            }
        } ?>

        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Nom</th>

                    <th class="dropdown">

                        <button class="drop-btn">
                            <?= $selectedCategory ? htmlspecialchars($selectedCategory) : "Toutes catégories" ?>
                            <img class="chevron" src="./assets/img/chevron-down-solid-full.svg" alt="chevron">
                        </button>

                        <div class="dropdown-content">
                            <a href="?page=admin&section=products">Toutes catégories</a>

                            <?php foreach ($categories as $category) { ?>
                                <a href="?page=admin&section=products&category=<?= urlencode($category) ?>">
                                    <?= htmlspecialchars($category) ?>
                                </a>
                            <?php } ?>
                        </div>
                    </th>

                    <th>Prix</th>
                    <th>Disponibilité</th>
                </tr>
            </thead>



            <tbody>
                <?php foreach ($data as $product) { ?>

                    <?php
                    if (
                        $selectedCategory !== null
                        && $product["category"] !== $selectedCategory
                    ) {
                        continue;
                    }
                    ?>

                    <tr>
                        <td><img src="./assets/img/products<?= htmlspecialchars($product["image"]) ?>" alt="<?= htmlspecialchars($product["name"]) ?>"></td>
                        <td><?= htmlspecialchars($product["name"]) ?></td>

                        <td><?= htmlspecialchars($product["category"]) ?></td>

                        <td><?= htmlspecialchars($product["price"]) ?> €</td>

                        <td>
                            <?= $product["is_available"]
                                ? "Disponible"
                                : "Indisponible" ?>
                        </td>
                    </tr>

                <?php } ?>
            </tbody>
        </table>
    </div>
</div>