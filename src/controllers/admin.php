<?php

class AdminController
{
    private AdminModel $model;
    private string $section = "dashboard";
    private array $sections = ["dashboard", "products", "menus", "users", "orders"];

    public function __construct(AdminModel $model)
    {
        $this->model = $model;
    }

    //Information de l'utilisateur qui se connecte
    public function getUsername(): string
    {
        return $_SESSION["first_name"];
    }

    public function getUserRole(): string
    {
        return $_SESSION["role"];
    }


    //appel avant affichage
    public function handleRequest(): void
    {
        //Que admin autorisé
        if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
            header("Location: index.php?page=login");
            exit;
        }

        //URL
        $section = $_GET["section"] ?? "dashboard";

        if (in_array($section, $this->sections, true)) {
            $this->section = $section;
        }

        //Vérif des données
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            header("Content-Type: application/json; charset=utf-8");

            $action = $_GET["action"] ?? "";

            //Modif d'un produit
            if ($action === "update-product" && $this->section === "products") {

                // Récupérer les données envoyées par le formulaire
                $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
                $name = trim($_POST["name"] ?? "");
                $description = trim($_POST["description"] ?? "");
                $priceValue = $_POST["price"] ?? "";
                $image = trim($_POST["image"] ?? "");
                $availableValue = $_POST["is_available"] ?? "";

                if (
                    !$id
                    || $name === ""
                    || !is_numeric($priceValue)
                    || (float) $priceValue < 0
                    || $image === ""
                    || !in_array($availableValue, ["0", "1"], true)
                ) {
                    http_response_code(400);

                    echo json_encode([
                        "success" => false,
                        "message" => "Les données du produit sont invalides"
                    ]);

                    exit;
                }

                // Envoyer les données au Model
                $success = $this->model->updateProduct(
                    $id,
                    $name,
                    $description,
                    (float) $priceValue,
                    $image,
                    (int) $availableValue
                );

                if (!$success) {
                    http_response_code(404);

                    echo json_encode([
                        "success" => false,
                        "message" => "Produit introuvable"
                    ]);

                    exit;

                }


                echo json_encode([
                    "success" => true,
                    "message" => "Produit modifié avec succès"
                ]);

                exit;
            }

            //Modif d'un menu
            if ($action === "update-menu" && $this->section === "menus") {

                // Récupérer les données envoyées par le formulaire
                $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
                $name = trim($_POST["name"] ?? "");
                $description = trim($_POST["description"] ?? "");
                $priceValue = $_POST["price"] ?? "";
                $image = trim($_POST["image"] ?? "");
                $availableValue = $_POST["is_available"] ?? "";

                // Récupérer les options cochées.
                $options = $_POST["options"] ?? [];

                // Vérifier que les options reçues sont bien des identifiants.
                if (!is_array($options)) {
                    $options = [];
                }
                $validOptions = [];

                foreach ($options as $optionId) {
                    $optionId = filter_var(
                        $optionId,
                        FILTER_VALIDATE_INT
                    );

                    if ($optionId === false || $optionId <= 0) {
                        http_response_code(400);

                        echo json_encode([
                            "success" => false,
                            "message" => "Une option est invalide."
                        ]);

                        exit;
                    }

                    // Éviter d'enregistrer plusieurs fois la même option.
                    $validOptions[] = $optionId;
                }

                $options = array_values(array_unique($validOptions));

                if (
                    !$id
                    || $name === ""
                    || !is_numeric($priceValue)
                    || (float) $priceValue < 0
                    || $image === ""
                    || !in_array($availableValue, ["0", "1"], true)
                ) {
                    http_response_code(400);

                    echo json_encode([
                        "success" => false,
                        "message" => "Les données du menu sont invalides"
                    ]);

                    exit;
                }

                // Envoyer les données au Model
                $success = $this->model->updateMenu(
                    $id,
                    $name,
                    $description,
                    (float) $priceValue,
                    $image,
                    (int) $availableValue,
                    $options
                );

                if (!$success) {
                    http_response_code(404);

                    echo json_encode([
                        "success" => false,
                        "message" => "Produit introuvable"
                    ]);

                    exit;

                }

                echo json_encode([
                    "success" => true,
                    "message" => "Produit modifié avec succès"
                ]);

                exit;
            }


            //Modif d'un utilisateur
            elseif ($action === "update-user" && $this->section === "users") {

                // Récupérer les données envoyées par le formulaire
                $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
                $firstname = trim($_POST["first_name"] ?? "");
                $lastname = trim($_POST["last_name"] ?? "");
                $role = trim($_POST["role"] ?? "");

                // Vérifier les données reçues
                if (
                    !$id
                    || !is_string($firstname)
                    || $firstname === ""
                    || mb_strlen($firstname) > 100
                    || !is_string($lastname)
                    || $lastname === ""
                    || mb_strlen($lastname) > 100
                ) {
                    http_response_code(400);

                    echo json_encode([
                        "success" => false,
                        "message" => "Les informations de l'utilisateur sont invalides"
                    ]);

                    exit;
                }

                // Envoyer les données au Model
                $success = $this->model->updateUser(
                    $id,
                    $firstname,
                    $lastname,
                    $role
                );

                if (!$success) {
                    http_response_code(404);

                    echo json_encode([
                        "success" => false,
                        "message" => "Utilisateur introuvable"
                    ]);

                    exit;
                }

                echo json_encode([
                    "success" => true,
                    "message" => "Utilisateur modifié avec succès"
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

    //Informations des screens
    public function getSection(): string
    {
        return $this->section;
    }


 // Récupérer les options dans la modale des menus.
    public function getAvailableProducts(): array
    {
        return $this->model->getAvailableProducts();
    }


    public function getData(): array
    {
        if ($this->section === "products") {
            return $this->model->getProducts();
        }
        if ($this->section === "menus") {
            return $this->model->getMenus();
        }
        if ($this->section === "users") {
            return $this->model->getUsers();
        }
        if ($this->section === "orders") {
            return $this->model->getOrders();
        }

        return $this->model->getStats();
    }

}

?>