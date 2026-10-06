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

    }

    //Informations des screens
    public function getSection(): string
    {
        return $this->section;
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