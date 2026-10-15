<div id="dashboard">
    <h1>Tableau de bord</h1>
    <ul class="dashboard-cards">
        <li class="card">
            <a href="index.php?page=prep&amp;section=toprepare">
                <h4>À préparer</h4>
                <p><?= (int) $stats["to_prepare"] ?> </p>
            </a>
        </li>

        <li class="card">
            <a href="index.php?page=prep&amp;section=prepared">
                <h4>Préparées</h4>
                <p><?=  (int) $stats["prepared"] ?></p>
            </a>
        </li>

        <li class="card">
            <a href="index.php?page=prep&amp;section=delivered">
                <h4>Livrées</h4>
                <p><?=  (int) $stats["delivered"] ?></p>
            </a>
        </li>
    </ul>
</div>