<?php
$firstName = $_SESSION["first_name"] ?? "";
$lastName = $_SESSION["last_name"] ?? "";
$email = $_SESSION["email"] ?? "";
?>


<header>
    <img id="logo" src="./assets/img/logo.png" alt="logo wacdo">

    <nav>
        <ul>
            <li>
                <p class="name" id="header-name"><?= htmlspecialchars($user_name) ?></p>
            </li>
            <li>
                <p class="role"><?= htmlspecialchars($user_role) ?></p>
            </li>
            <li>
                <button type="button" class="button" id="open-account" aria-label="Mon compte">Mon compte</button>
            </li>
            <li>
                <a class="button" id="connect" href="index.php?page=logout">Déconnexion </a>
            </li>
        </ul>
    </nav>
</header>


<!-- Modale pour la modification de mon compte -->
<dialog id="account-modal">
    <h2>Mon compte</h2>
    <p>Email : <?= htmlspecialchars($email) ?></p>

    <form id="account-form">
        <label for="account-firstname">Prénom</label>
        <input type="text" id="account-firstname" name="first_name" maxlength="100"
            value="<?= htmlspecialchars($firstName) ?>" required>

        <label for="account-lastname">Nom</label>
        <input type="text" id="account-lastname" name="last_name" maxlength="100"
            value="<?= htmlspecialchars($lastName) ?>" required>

        <div class="modal-actions">
            <button type="button" id="cancel-account-modal" class="cancel-button">Annuler</button>
            <button type="submit" id="save-account-modal" class="save-button">Enregistrer</button>
        </div>
    </form>

    <form id="password-form">
        <h3>Changer mon mot de passe</h3>

        <label for="current-password">Mot de passe actuel</label>
        <input type="password" id="current-password" name="current_password" autocomplete="current-password" required>

        <label for="new-password">Nouveau mot de passe (12 caractères minimum)</label>
        <input type="password" id="new-password" name="new_password" autocomplete="new-password" minlength="12"
            required>

        <label for="confirm-password">Confirmation</label>
        <input type="password" id="confirm-password" name="confirm_password" autocomplete="new-password" required>

        <button type="submit" class="save-button">Modifier le mot de passe</button>
    </form>

    <form id="delete-form">
        <h3>Supprimer mon compte</h3>
        <p>Attention, cette action est définitive.</p>

        <label for="delete-password">Mot de passe</label>
        <input type="password" id="delete-password" name="password" autocomplete="current-password" required>

        <div class="modal-actions">
            <button type="submit" class="danger-button">Supprimer mon compte</button>
        </div>
    </form>

    <form method="dialog" class="modal-actions">
        <button id="cancel-account-modal">Annuler</button>
    </form>
</dialog>


<script src="./assets/js/account.js" defer></script>