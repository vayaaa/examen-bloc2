<?php

class AccountController
{
    private AccountModel $model;

    public function __construct(AccountModel $model)
    {

        $this->model = $model;
    }

    // Appelé par index.php quand l'URL contient page=account
    public function handleRequest(): void
    {
        //Toujours une réponse JSON
        header("Content-Type: application/json; charset=utf-8");

        //Il faut être connecté avec n'importe quel rôle 
        if (!isset($_SESSION["user_id"])) {
            http_response_code(401);

            echo json_encode([
                "success" => false,
                "message" => "Vous devez être connecté"
            ]);

            exit;
        }

        //Que des formulaires envoyés en POST
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            http_response_code(405);

            echo json_encode([
                "success" => false,
                "message" => "Méthode non autorisée"
            ]);

            exit;
        }

        $userId = (int) $_SESSION["user_id"];
        $action = $_GET["action"] ?? "";


        //Modification du nom et prénom________________________________________________
        if ($action === "update-profile") {

            $firstname = trim($_POST["first_name"] ?? "");
            $lastname = trim($_POST["last_name"] ?? "");


            // Vérifier les données reçues
            if (
                $firstname === ""
                || mb_strlen($firstname) > 100
                || $lastname === ""
                || mb_strlen($lastname) > 100
            ) {
                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "message" => "Les données utilisateurs sont invalides"
                ]);

                exit;
            }

            $success = $this->model->updateProfile(
                $userId,
                $firstname,
                $lastname
            );

            if (!$success) {
                http_response_code(404);

                echo json_encode([
                    "success" => false,
                    "message" => "Impossible de modifier l'utilisateur"
                ]);

                exit;
            }

            // Màj de la session pour que le header affiche le nouveau prénom
            $_SESSION["first_name"] = $firstname;
            $_SESSION["last_name"] = $lastname;

            echo json_encode([
                "success" => true,
                "message" => "Informations modifiées avec succès"
            ]);

            exit;
        }


        //Modification du mot de passe________________________________________________
        elseif ($action === "change-password") {

            $currentPassword = $_POST["current_password"] ?? "";
            $newPassword = $_POST["new_password"] ?? "";
            $confirmPassword = $_POST["confirm_password"] ?? "";

            // Vérifier les données reçues
            if (
                $currentPassword === ""
                || $newPassword === ""
                || $confirmPassword === ""

                || $newPassword !== $confirmPassword
                || $newPassword === $currentPassword
                || mb_strlen($newPassword) < 12
                || strlen($newPassword) > 72

            ) {
                http_response_code(400);

                echo json_encode([
                    "success" => false,
                    "message" => "Mot de passe invalide: 12 caractères minimum, confirmation identique et différent de l'ancien mot de passe"
                ]);

                exit;
            }

            // Récupérer le hash enregistré en base
            $hash = $this->model->getPasswordHash($userId);

            // Vérifier l'ancien mot de passe
            if ($hash === false || !password_verify($currentPassword, $hash)) {
                http_response_code(403);

                echo json_encode([
                    "success" => false,
                    "message" => "Mot de passe actuel incorrect"
                ]);

                exit;
            }

            $success = $this->model->updatePassword(
                $userId,
                password_hash($newPassword, PASSWORD_DEFAULT)
            );

            session_regenerate_id(true);// Nouvel identifiant de session après un changement sensible
            
            if (!$success) {
                http_response_code(500);

                echo json_encode([
                    "success" => false,
                    "message" => "Impossible de modifier le mot de passe"
                ]);

                exit;
            }
            echo json_encode([
                "success" => true,
                "message" => "Mot de passe modifié avec succès"
            ]);

            exit;
        }

        //Suppression des données du compte________________________________________________
        elseif ($action === "delete-account") {

            $password = $_POST["password"] ?? "";
            $hash = $this->model->getPasswordHash($userId);

            if ($password === "" || $hash === false || !password_verify($password, $hash)) {
                http_response_code(403);

                echo json_encode([
                    "success" => false,
                    "message" => "Mot de passe incorrect"
                ]);

                exit;
            }

            //Refuser la supp si c'est le seul compte actif 
            if ($this->model->countActiveByRole($_SESSION["role"]) <= 1) {
                http_response_code(409);

                echo json_encode([
                    "success" => false,
                    "message" => "Vous êtes le seul utilisateur avec ce rôle. Attribuez d'abord ce rôle à un autre utilisateur."
                ]);

                exit;
            }

            $this->model->deleteUser($userId);

            // on vide la session puis on la détruit
            $_SESSION = [];
            session_destroy();

            echo json_encode([
                "success" => true,
                "message" => "Compte supprimé"
            ]);

            exit;
        }
        // Action inconnue
        else {
            http_response_code(400);

            echo json_encode([
                "success" => false,
                "message" => "Action inconnue"
            ]);

            exit;

        }
    }
}



?>