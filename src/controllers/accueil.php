<?php
class Accueilcontroller
{
    private AccueilModel $model;
    private string $section = "dashboard";
    private array $sections = ["dashboard", "neworder", "prepared"];
    private string $errorMessage = "";


    public function __construct(AccueilModel $model)
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
        //Que accueil autorisé
        if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "accueil") {
            header("Location: index.php?page=login");
            exit;
        }

        //URL
        $section = $_GET["section"] ?? "dashboard";

        if (in_array($section, $this->sections, true)) {
            $this->section = $section;
        }

        //Formulaire envoyé ?
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Le champ caché "action" du formulaire dit lequel c'est
            $action = $_POST["action"] ?? "";

            if ($action === "deliver") {
                $this->deliverOrder();
            }

            if ($action === "create_order") {
                $this->createOrder();
            }
        }
    }



    // Remise d'une commande au client: préparée -> livrée
    private function deliverOrder(): void
    {
        $orderId = (int) ($_POST["order_id"] ?? 0);

        if ($orderId > 0) {
            $this->model->deliverOrder($orderId);
        }

        header("Location: index.php?page=accueil&section=prepared");
        exit;
    }


    // Enregistre une nouvelle commande.
    private function createOrder(): void
    {
        // que dit le formulaire a envoyé
        $source = $_POST["source"] ?? "";
        $time = $_POST["delivery_time"] ?? "";
        $products = $_POST["products"] ?? [];
        $menuLines = $_POST["menu_lines"] ?? [];

        // Vérification 
        if (!in_array($source, ["comptoir", "telephone"], true)) {
            $this->errorMessage = "Choisissez comptoir ou téléphone.";
            return;
        }

        if (!is_string($time) || !preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9]$/", $time)) {
            $this->errorMessage = "Heure de livraison invalide.";
            return;
        }

        // Les quantités sont sous forme de tableaux
        if (!is_array($products) || !is_array($menuLines)) {
            $this->errorMessage = "Commande invalide.";
            return;
        }

        $deliveryTime = date("Y-m-d") . " " . $time . ":00";

        //enregistre sauf si erreur
        try {
            $ticket = $this->model->newOrder(
                (int) $_SESSION["user_id"],
                $source,
                $deliveryTime,
                $products,
                $menuLines
            );

        } catch (InvalidArgumentException $e) {
            // Erreur "normale" (commande vide, produit indisponible...), on affiche le message
            $this->errorMessage = $e->getMessage();
            return;

        } catch (Exception $e) {
            // Erreur technique (base de données...), on ne montre pas le détail à l'utilisateur 
            error_log($e->getMessage());
            $this->errorMessage = "Erreur technique: la commande n'a pas été enregistrée.";
            return;
        }

        //Enregistré
        $_SESSION["success_message"] = "Commande enregistrée. Numéro de ticket : " . $ticket;
        header("Location: index.php?page=accueil&section=neworder");
        exit;
    }


    //Informations des screens
    public function getSection(): string
    {
        return $this->section;
    }


    //Commandes prêtes
    public function getOrders(): array
    {
        if ($this->section !== "prepared") {
            return [];
        }
        return $this->model->getOrders("prepared");
    }


    // Les compteurs du tableau de bord
    public function getStats(): array
    {
        if ($this->section !== "dashboard") {
            return [];
        }

        return $this->model->getStats();
    }


    // Les catégories (seulement dans la section "neworder")
    public function getCategories(): array
    {
        if ($this->section !== "neworder") {
            return [];
        }

        return $this->model->getCategories();
    }


    // Les produits et menus du formulaire
    public function getProducts(): array
    {
        if ($this->section !== "neworder") {
            return [];
        }

        return $this->model->getProducts();
    }
    public function getMenus(): array
    {
        if ($this->section !== "neworder") {
            return [];
        }

        return $this->model->getMenus();
    }


    //Récupérer les messages à afficher à l'utilisateur
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
    public function getSuccessMessage(): string
    {
        $message = $_SESSION["success_message"] ?? "";
        unset($_SESSION["success_message"]);

        return $message;
    }

}


?>