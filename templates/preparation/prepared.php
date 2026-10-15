<div id="toprepare">
    <h1>Prêtes</h1>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Heure</th>
                    <th>Source</th>
                    <th>Produits</th>
                </tr>
            </thead>

            <tbody>
                <!-- Si pas de commande -->
                <?php if (empty($orders)) { ?>
                    <p>Aucune commande à préparer.</p>
                <?php } ?>
                <?php foreach ($orders as $order) { ?>
                    <tr>

                        <td> <?= htmlspecialchars($order["ticket_number"]) ?></td>
                        <td><?= date("H:i", strtotime($order["order_date"])) ?></td>
                        <td> <?= htmlspecialchars($order["source"]) ?></td>
                        <td>
                            <ul>
                                <!-- Les items de la commandes -->
                                <?php foreach ($order["items"] as $item) { ?>
                                    <li><?= (int) $item["quantity"] ?> × <?= htmlspecialchars($item["name"]) ?></li>
                                <?php } ?>
                            </ul>
                        </td>
                    </tr>

                <?php } ?>
            </tbody>
        </table>
    </div>
</div>