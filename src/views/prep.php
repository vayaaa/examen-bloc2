<?php

class PrepView
{

    private PrepController $controller;
    public string $template;


    public function __construct(PrepController $controller)
    {
        $this->controller = $controller;
        $this->template = DIR_TEMPLATES . "prep.php";
    }


    public function render()
    {
        $user_name = $this->controller->getUserName();
        $user_role = $this->controller->getUserRole();
        $section = $this->controller->getSection();
        $orders = $this->controller->getOrders();
        $stats = $this->controller->getStats();

        require($this->template);
    }

}

?>