<?php

class PrepController
{

    private PrepModel $model;
    private string $section = "dashboard";
    private array $sections = ["dashboard", "toprepare", "prepared", "delivered"];

    public function __construct(PrepModel $model)
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
        //Que preparation autorisé
        if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "preparation") {
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

            $orderId = (int) ($_POST["order_id"] ?? 0);

            if ($orderId > 0) {
                $this->model->pushToPrepared($orderId);
            }
            header("Location: index.php?page=prep&section=toprepare");
            exit;
        }
    }


    //Informations des screens
    public function getSection(): string
    {
        return $this->section;
    }


    public function getOrders(): array
    {
        if ($this->section === "dashboard") {
            return [];
        }

        $status = "to_prepare";

        if ($this->section === "prepared") {
            $status = "prepared";
        }

        if ($this->section === "delivered") {
            $status = "delivered";
        }

        return $this->model->getOrders($status);
    }
    // Les compteurs du tableau de bord
    public function getStats(): array
    {
        if ($this->section !== "dashboard") {
            return [];
        }

        return $this->model->getStats();
    }

}


?>