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
                    <th></th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($data as $user) { ?>
                    <tr>
                        <td><?= htmlspecialchars($user["first_name"]) ?></td>
                        <td> <?= htmlspecialchars($user["last_name"]) ?></td>
                        <td> <?= htmlspecialchars($user["email"]) ?></td>
                        <td> <?= htmlspecialchars($user["role"]) ?></td>
                        <td>
                            <button type="button" class="modifier modifier-user" data-id="<?= (int) $user["id"] ?>"
                                data-firstname="<?= htmlspecialchars($user["first_name"]) ?>"
                                data-lastname="<?= htmlspecialchars($user["last_name"]) ?>"
                               data-role="<?= (int) $user["role_id"] ?>">
                                <img class="modifier" src="./assets/img/pen-solid-full.svg" alt="modifier">
                            </button>
                        </td>
                    </tr>
                <?php } ?>

            </tbody>
        </table>
    </div>
</div>



<!-- Modale pour la modification des utilisateurs -->
<dialog id="user-modal">
    <h2 id="modal-title">Modifier</h2>

    <form id="user-form">
        <input type="hidden" id="user-id" name="id">

        <label for="user-firstname">Nom</label>
        <input type="text" id="user-firstname" name="first_name" maxlength="100" required>

        <label for="user-lastname">Prénom</label>
        <input type="text" id="user-lastname" name="last_name" maxlength="100" required>

        <label for="user-role">Rôle</label>
        <select name="role" id="user-role">
            <option value="1">Administrateur</option>
            <option value="3">Accueil</option>
            <option value="2">Préparation</option>
        </select>

        <div class="modal-actions">
            <button type="button" id="cancel-user-modal">Annuler</button>
            <button type="submit" id="save-user-modal" class="save-button">Enregistrer</button>
        </div>
    </form>
</dialog>