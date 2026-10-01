<header>
    <img id="logo" src="./assets/img/logo.png" alt="logo wacdo">

    <nav>
        <ul>
            <li>
                <p class="name"><?= htmlspecialchars($user_name) ?> </p>
            </li>
            <li>
                <p class="role"><?= htmlspecialchars($user_role)?> </p>
            </li>
            <li>
                <a class="button" id="connect" href="index.php?page=login">
                    Déconnexion
                </a>
            </li>
        </ul>
    </nav>
</header>