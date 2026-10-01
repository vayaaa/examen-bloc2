<?php

class AdminController
{
    private AdminModel $model;

    public function __construct(AdminModel $model)
    {
        $this->model = $model;
    }

    public function getUsername() : string
    {
        return $_SESSION["first_name"];
    }

    public function getUserRole() : string
    {
        return $_SESSION["role"];
    }

}

?>