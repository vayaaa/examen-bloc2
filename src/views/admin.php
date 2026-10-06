<?php

class AdminView
{

    public AdminController $controller;
    public string $template;


    public function __construct(AdminController $controller)
    {
        $this->controller = $controller;
        $this->template = DIR_TEMPLATES . "admin.php";
    }

    public function render()
    {
        $user_name = $this->controller->getUserName();
        $user_role = $this->controller->getUserRole();

        $section = $this->controller->getSection();
        $data = $this->controller->getData();

        require($this->template);

    }

}


?>