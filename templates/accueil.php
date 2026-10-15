<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Wacdo - Acceuil</title>
    <link rel="stylesheet" href="./assets/css/main.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap"
        rel="stylesheet">
</head>
<body>
    <?= include(DIR_TEMPLATES . "header.php") ?>

    <main class="accueil-container">
        <section id="menu">
            <ul>
                <li>
                    <a class="elements <?= $section === 'dashboard' ? 'selected' : '' ?>"
                        href="index.php?page=accueil&amp;section=dashboard"> Tableau de bord </a>
                </li>
                <li>
                    <a class="elements <?= $section === 'prepared' ? 'selected' : '' ?>"
                        href="index.php?page=accueil&amp;section=prepared">Commandes prêtes</a>
                </li>
                <li>
                    <a class="elementsNew <?= $section === 'neworder' ? 'selected' : '' ?>"
                        href="index.php?page=accueil&amp;section=neworder">
                        <img id="plus" src="./assets/img/plus-solid-full.svg" alt=" icon ajouter">Nouvelle commandes</a>
                </li>
            </ul>
        </section>

        <section id="screen">
            <?php include(DIR_TEMPLATES . "accueil/" . $section . ".php"); ?>
        </section>
    </main>

</body>
</html>