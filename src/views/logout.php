<?php

class LogoutView
{

    private $controller;
    private $template;

    public function __construct(LogoutController $controller)
    {
        $this->controller = $controller;
        $this->template = DIR_TEMPLATES . "logout.php";
    }


    public function render()
    {
        $controller = $this->controller;


        include($this->template);
    }
}


?>