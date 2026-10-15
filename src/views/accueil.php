<?php

class AccueilView
{
    private Accueilcontroller $controller;
    private string $template;

    public function __construct(Accueilcontroller $controller)
    {
        $this->controller = $controller;
        $this->template = DIR_TEMPLATES . "accueil.php";
    }

    public function render()
    {
        $user_name = $this->controller->getUserName();
        $user_role = $this->controller->getUserRole();
        $section = $this->controller->getSection();


        $orders = $this->controller->getOrders();
        $stats = $this->controller->getStats();
        $categories = $this->controller->getCategories();
        $products = $this->controller->getProducts();
        $menus = $this->controller->getMenus();
        $errorMessage = $this->controller->getErrorMessage();
        $successMessage = $this->controller->getSuccessMessage();

        require($this->template);
    }

}


?>