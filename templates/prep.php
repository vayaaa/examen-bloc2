<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Wacdo - Préparation</title>
    <link rel="stylesheet" href="./assets/css/main.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <?php include(DIR_TEMPLATES . "header.php"); ?>

    <main class="prep-container">
        <section id="menu">
            <ul>
                <li>
                    <a class="elements <?= $section === 'dashboard' ? 'selected' : '' ?>"
                       href="index.php?page=prep&amp;section=dashboard"> Tableau de bord </a>
                </li>
                <li>
                    <a class="elements <?= $section === 'toprepare' ? 'selected' : '' ?>"
                        href="index.php?page=prep&amp;section=toprepare">À préparer</a>
                </li>
                 <li>
                    <a class="elements <?= $section === 'prepared' ? 'selected' : '' ?>"
                        href="index.php?page=prep&amp;section=prepared">Prêtes</a>
                </li>
                 <li>
                    <a class="elements <?= $section === 'delivered' ? 'selected' : '' ?>"
                        href="index.php?page=prep&amp;section=delivered">Livrées</a>
                </li>

            </ul>
        </section>

        <section id="screen">
            <?php include(DIR_TEMPLATES . "preparation/" . $section . ".php"); ?>
        </section>
    </main>


</body>
</html>