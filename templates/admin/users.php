<div id="users">

    <h1>Utilisateurs</h1>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($data as $user) { ?>
                    <tr>
                        <td><?= htmlspecialchars($user["first_name"]) ?></td>
                        <td> <?= htmlspecialchars($user["last_name"]) ?></td>
                        <td> <?= htmlspecialchars($user["email"]) ?></td>
                        <td> <?= htmlspecialchars($user["role"]) ?></td>
                    </tr>
                <?php } ?>

            </tbody>
        </table>
    </div>
</div>