<?php

class AdminModel
{
    private PDO $db;


    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findUser(string $email): array|false
    {
        $sql = "SELECT users.id, users.first_name, roles.name AS role
                FROM users
                JOIN roles ON users.role_id = roles.id
                WHERE users.email = :email";

        $query = $this->db->prepare($sql);
        $query->bindValue(":email", $email);
        $query->execute();

        return $query->fetch();
    }

}

?>