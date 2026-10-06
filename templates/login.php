<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Wacdo - Connexion</title>
    <link rel="stylesheet" href="./assets/css/main.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:ital,wght@0,200..900;1,200..900&display=swap"
        rel="stylesheet">
</head>

<body>

    <main class="login-container">
        <img id="logo" src="./assets/img/logo.png" alt="logo wacdo">

        <section id="login">
            <h1>Connexion</h1>

            <?php if (htmlspecialchars($errorMessage) !== "") { ?>
                <p class="error"><?= $errorMessage ?> </p>

            <?php } ?>

            <form action="" method="post">
                <div class="form-group">
                    <label for="inputEmail">Email</label>
                    <input type="email" name="email" id="inputEmail" placeholder="Email" required />
                </div>

                <div class="form-group">
                    <label for="inputPassword">Mot de passe</label>
                    <input type="password" name="password" id="inputPassword" placeholder="Mot de passe" required />
                    <a class="reset-password" href="./reset-password">mot de passe oublié?</a>

                </div>


                <input class="connect-button" type="submit" value="Se connecter" />

            </form>

        </section>





    </main>


</body>

</html>