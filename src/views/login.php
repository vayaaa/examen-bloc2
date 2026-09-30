<?php

class LoginView
{

    public LoginController $controller;
    public string $template;


    public function __construct(LoginController $controller)
    {
        $this->controller = $controller;
        $this->template = DIR_TEMPLATES . "login.php";

    }

    public function render()
    {
        $errorMessage = $this->controller->getErrorMessage();

        require($this->template);

    }

}


?>