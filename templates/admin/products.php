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
                    <th></th>
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
                        <td><img src="./assets/img/products<?= htmlspecialchars($product["image"]) ?>"
                                alt="<?= htmlspecialchars($product["name"]) ?>"></td>
                        <td><?= htmlspecialchars($product["name"]) ?></td>

                        <td><?= htmlspecialchars($product["category"]) ?></td>

                        <td><?= htmlspecialchars($product["price"]) ?> €</td>

                        <td>
                            <?= $product["is_available"]
                                ? "Disponible"
                                : "Indisponible" ?>
                        </td>

                        <td>
                            <button type="button" class="modifier-product" data-id="<?= (int) $product["id"] ?>"
                                data-name="<?= htmlspecialchars($product["name"]) ?>"
                                data-description="<?= htmlspecialchars($product['description'] ?? '') ?>"
                                data-price="<?= htmlspecialchars((string) $product['price']) ?>"
                                data-image="<?= htmlspecialchars($product['image']) ?>"
                                data-available="<?= (int) $product['is_available'] ?>">
                                <img class="modifier" src="./assets/img/pen-solid-full.svg" alt="modifier">
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>



<!-- Modale pour la modification de produits -->
<dialog id="modal">
    <h2>Modifier</h2>

    <form id="product-form">
        <input type="hidden" id="product-id" name="id">

        <label for="product-name">Nom</label>
        <input type="text" id="product-name" name="name" required>

        <label for="product-image">Image (nom du fichier)</label>
        <input type="text" id="product-image" name="image" required>

        <label for="product-description">Description</label>
        <textarea id="product-description" name="description"></textarea>

        <label for="product-price">Prix</label>
        <input type="number" id="product-price" name="price" min="0" step="0.01" required>

        <label for="product-available">Disponibilité</label>
        <select name="is_available" id="product-available">
            <option value="1">Disponible</option>
            <option value="0">Indisponible</option>
            <option value="0">Rupture</option>
        </select>

        <div class="modal-actions">
            <button type="button" id="cancel-modal">Annuler</button>
            <button type="submit" id="save-modal" class="save-button">Enregistrer</button>
        </div>
    </form>
</dialog>