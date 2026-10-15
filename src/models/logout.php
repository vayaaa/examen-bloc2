<?php

class LogoutModel
{

    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
}

?>