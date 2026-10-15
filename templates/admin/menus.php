<div id="menus">
    <h1>Produits</h1>

    <div class="table-container">

        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>Disponibilité</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($data as $menu) { ?>
                    <tr>
                        <td><img src="./assets/img/products<?= htmlspecialchars($menu["image"]) ?>"
                                alt="<?= htmlspecialchars($product["name"]) ?>"></td>
                        <td><?= htmlspecialchars($menu["name"]) ?></td>
                        <td><?= htmlspecialchars($menu["price"]) ?> €</td>
                        <td>
                            <?= $menu["is_available"]
                                ? "Disponible"
                                : "Indisponible" ?>
                        </td>
                        <td>
                            <button type="button" class="modifier-menu" data-id="<?= (int) $menu["id"] ?>"
                                data-name="<?= htmlspecialchars($menu["name"], ENT_QUOTES, 'UTF-8') ?>"
                                data-description="<?= htmlspecialchars($menu['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                data-price="<?= htmlspecialchars((string) $menu['price'], ENT_QUOTES, 'UTF-8') ?>"
                                data-image="<?= htmlspecialchars($menu['image'], ENT_QUOTES, 'UTF-8') ?>"
                                data-available="<?= (int) $menu['is_available'] ?>"
                                data-options="<?= htmlspecialchars($menu['options'] ?? '[]', ENT_QUOTES, 'UTF-8') ?>">
                                <img class="modifier" src="./assets/img/pen-solid-full.svg" alt="Modifier">
                            </button>
                        </td>
                    </tr>

                <?php } ?>
            </tbody>
        </table>
    </div>
</div>



<!-- Modale pour la modification des menus -->
<dialog id="menu-modal">
    <h2>Modifier</h2>

    <form id="menu-form">
        <input type="hidden" id="menu-id" name="id">

        <label for="menu-name">Nom</label>
        <input type="text" id="menu-name" name="name" required>

        <label for="menu-image">Image (nom du fichier)</label>
        <input type="text" id="menu-image" name="image" required>

        <label for="menu-description">Description</label>
        <textarea id="menu-description" name="description"></textarea>

        <label for="menu-price">Prix</label>
        <input type="number" id="menu-price" name="price" min="0" step="0.01" required>

        <fieldset>
            <legend>Options</legend>

            <?php foreach ($availableProducts as $product) { ?>
                <div>
                    <input type="checkbox" id="option-<?= (int) $product['id'] ?>" name="options[]"
                        value="<?= (int) $product['id'] ?>">

                    <label for="option-<?= (int) $product['id'] ?>">
                        <?= htmlspecialchars($product['name']) ?>
                    </label>
                </div>
            <?php } ?>
        </fieldset>

        <label for="menu-available">Disponibilité</label>
        <select name="is_available" id="menu-available">
            <option value="1">Disponible</option>
            <option value="0">Indisponible</option>
        </select>

        <div class="modal-actions">
            <button type="button" id="cancel-menu-modal">Annuler</button>
            <button type="submit" id="save-modal" class="save-button">Enregistrer</button>
        </div>
    </form>
</dialog>