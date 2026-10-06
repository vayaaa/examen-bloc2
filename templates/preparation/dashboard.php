<div id="dashboard">
    <h1>Tableau de bord</h1>
    <ul class="dashboard-cards">
        <li class="card">
            <a href="index.php?page=admin&amp;section=products">
                <h4>Produits</h4>
                <p><?= $data["products"] ?> </p>
            </a>
        </li>

        <li class="card">
            <a href="index.php?page=admin&amp;section=menus">
                <h4>Menus</h4>
                <p><?= $data["menus"] ?></p>
            </a>
        </li>

        <li class="card">
            <a href="index.php?page=admin&amp;section=orders">
                <h4>Commandes en cours</h4>
                <p><?= $data["order_to_prepare"] ?></p>
            </a>
        </li>
    </ul>
</div>