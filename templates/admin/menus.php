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
                </tr>
            </thead>

            <tbody>
                <?php foreach ($data as $menu) { ?>
                    <tr>
                        <td><img src="./assets/img/products<?= htmlspecialchars($menu["image"]) ?>" alt="<?= htmlspecialchars($product["name"]) ?>"></td>
                        <td><?= htmlspecialchars($menu["name"]) ?></td>
                        <td><?= htmlspecialchars($menu["price"]) ?> €</td>
                        <td>
                            <?= $menu["is_available"]
                                ? "Disponible"
                                : "Indisponible" ?>
                        </td>
                    </tr>

                <?php } ?>
            </tbody>
        </table>
    </div>
</div>