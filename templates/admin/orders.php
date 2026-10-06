<div id="orders">
    <h1>Commandes</h1>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Numéro</th>
                    <th>Status</th>
                    <th>Source</th>
                    <th>Prix total</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($data as $order) { ?>
                    <tr>
                        <td><?= htmlspecialchars($order["ticket_number"]) ?></td>
                        <td> <?= htmlspecialchars($order["status"]) ?></td>
                        <td> <?= htmlspecialchars($order["source"]) ?></td>
                        <td> <?= htmlspecialchars($order["order_date"]) ?></td>
                        <td> <?= htmlspecialchars($order["total_price"]) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>