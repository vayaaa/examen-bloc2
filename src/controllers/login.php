<?php

class LoginController
{
    private LoginModel $model;
    private string $errorMessage = "";

    public function __construct(LoginModel $model)
    {
        $this->model = $model;
    }


    // Appelé avant l'affichage
    public function handleRequest(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && $this->validateLogin()) {
            $this->redirectByRole();
        }
    }

    //Vérifie les infos saisie par l'utilisateur et validation ou non de l'ouverture de session
    public function validateLogin(): bool
    {
        $email = trim(strip_tags($_POST['email'] ?? ""));
        $password = $_POST['password'] ?? "";

        if ($email === "" || $password === "") {
            $this->errorMessage = "Veuillez remplir tous les champs.";
            return false;
        }

        $user = $this->model->findbyEmail($email);

        //Vérifiacation du mail et du mdp + on ne révèle pas si l'email existe en cas d'erreur
        if ($user === false || !password_verify($password, $user["password"])) {
            $this->errorMessage = "Email ou mot de passe incorrect.";
            return false;
        }

        session_regenerate_id(true); //génération d'un nouvel id de session et supprime l'ancienne
        //Enregistrer les infos dans la session
        $_SESSION["user_id"] = (int) $user["id"];
        $_SESSION["first_name"] = $user["first_name"];
        $_SESSION["last_name"] = $user["last_name"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["role"] = $user["role"];

        return true;
    }

    //Redirection vers la bonne page selon le role
    private function redirectByRole(): void
    {
        $role = $_SESSION["role"];

        if ($role === "admin") {
            header("location: index.php?page=admin");
        } elseif ($role === "preparation") {
            header("location: index.php?page=prep");
        } elseif ($role === "accueil") {
            header("location: index.php?page=accueil");
        } else {
            $_SESSION = [];
            session_destroy();
            header("Location: index.php?page=login");
        }
        exit;
    }


    //Retourner les messages d'erreurs
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}

?>