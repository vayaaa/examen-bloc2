<div id="neworder">
    <h1>Nouvelle commande</h1>

    <?php if ($errorMessage !== "") { ?>
        <p class="message error"><?= htmlspecialchars($errorMessage) ?></p>
    <?php } ?>

    <?php if ($successMessage !== "") { ?>
        <p class="message success"><?= htmlspecialchars($successMessage) ?></p>
    <?php } ?>

    <div class="order-layout">
        <section class="catalog">

            <?php /* Catégories */ ?>
            <section class="categories">
                <div id="categories-container">
                    <?php foreach ($categories as $category) { ?>
                        <button type="button" class="item" data-category="<?= htmlspecialchars($category["name"]) ?>"
                            data-label="<?= htmlspecialchars(ucfirst($category["name"])) ?>">
                            <img src="./assets/img<?= htmlspecialchars($category["image"] ?? "") ?>" alt="">
                            <span><?= htmlspecialchars(ucfirst($category["name"])) ?></span>
                        </button>
                    <?php } ?>
                </div>
            </section>

            <?php /* Produits */ ?>
            <section class="products">
                <h2 id="categorie-title"></h2>

                <div id="products-container">
                    <?php /* menus*/ ?>
                    <?php foreach ($menus as $menu) { ?>
                        <button type="button" class="item" data-type="menu" data-category="menus"
                            data-id="<?= (int) $menu['id'] ?>"
                            data-name="<?= htmlspecialchars($menu['name'], ENT_QUOTES, 'UTF-8') ?>"
                            data-price="<?= htmlspecialchars($menu['price'], ENT_QUOTES, 'UTF-8') ?>" 
                            data-options="<?= htmlspecialchars($menu['options'] ?? '[]',ENT_QUOTES,'UTF-8') ?>">
                            <img src="./assets/img/products<?= htmlspecialchars($menu['image']) ?>" alt="">
                            <span class="name"><?= htmlspecialchars($menu['name']) ?></span>
                            <span class="price">
                                <?= number_format((float) $menu['price'], 2, ',', ' ') ?> €
                            </span>
                        </button>
                    <?php } ?>

                    <?php /* produits*/ ?>
                    <?php foreach ($products as $categoryName => $items) { ?>
                        <?php foreach ($items as $product) { ?>

                            <button type="button" class="item" data-type="product"
                                data-category="<?= htmlspecialchars($categoryName) ?>" data-id="<?= (int) $product["id"] ?>"
                                data-name="<?= htmlspecialchars($product["name"]) ?>"
                                data-price="<?= htmlspecialchars($product["price"]) ?>" hidden>
                                <img src="./assets/img/products<?= htmlspecialchars($product["image"]) ?>" alt="">
                                <span class="name"><?= htmlspecialchars($product["name"]) ?></span>
                                <span class="price"><?= number_format((float) $product["price"], 2, ",", " ") ?> €</span>
                            </button>
                        <?php } ?>
                    <?php } ?>

                </div>
            </section>
        </section>


        <?php /* Panier */ ?>
        <aside class="cart">
            <h2>Panier</h2>

            <form method="post" action="index.php?page=accueil&amp;section=neworder">
                <input type="hidden" name="action" value="create_order">

                <div class="source">
                    <label><input type="radio" name="source" value="comptoir" checked> Comptoir</label>
                    <label><input type="radio" name="source" value="telephone"> Téléphone</label>
                </div>

                <label for="delivery_time">Heure de livraison</label>
                <input type="time" id="delivery_time" name="delivery_time"
                    value="<?= date("H:i", strtotime("+15 minutes")) ?>" required>

                <hr>

                <ul id="cart-lines"></ul>
                <p id="cart-empty">Le panier est vide.</p>

                <hr>

                <p class="order-total">Total : <span id="total">0,00</span> €</p>

                <button id="submit-button" class="ready-button" type="submit" disabled>Valider la commande</button>
            </form>
        </aside>
    </div>
</div>



<!-- Modale pour les menus -->
<dialog id="modal">
    <h2 id="modal-title"></h2>

    <h3>Choisissez une frite</h3>
    <div id="side-choices" class="modal-choices"></div>

    <h3>Choisissez une boisson</h3>
    <div id="drink-choices" class="modal-choices"></div>

    <div class="modal-actions">
        <button type="button" id="cancel-modal">Annuler</button>
        <button type="button" id="add-modal" class="ready-button" disabled>Ajouter à ma commande</button>
    </div>
</dialog>


<script src="./assets/js/newOrder.js"></script>