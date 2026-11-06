<?php

class AccountModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    //Modifier les infos utilisateurs
    public function updateProfile(int $id, string $firstname, string $lastname): bool
    {
        $sql = "UPDATE users
            SET first_name = :firstname, 
            last_name = :lastname
            WHERE id = :id";

        $query = $this->db->prepare($sql);

        return $query->execute([
            ":firstname" => $firstname,
            ":lastname" => $lastname,
            ":id" => $id
        ]);
    }


    //Récupération du hash
    public function getPasswordHash(int $id): string|false
    {
        $sql = "SELECT password 
        FROM users 
        WHERE id = :id";

        $query = $this->db->prepare($sql);

        $query->execute([
            ":id" => $id
        ]);

        return $query->fetchColumn();
    }

    //Modification du nouveau mot de passe 
    public function updatePassword(int $id, string $hash): bool
    {
        $sql = "UPDATE users 
        SET password = :password
        WHERE id = :id";

        $query = $this->db->prepare($sql);

        return $query->execute([
            ":password" => $hash,
            ":id" => $id
        ]);
    }


    // Compter les comptes actifs d'un rôle
    public function countActiveByRole(string $role): int
    {
        $sql = "SELECT COUNT(*)
        FROM users 
        JOIN roles ON users.role_id = roles.id
            WHERE roles.name = :role
            AND users.is_active = 1";


        $query = $this->db->prepare($sql);

        $query->execute([
            ":role" => $role
        ]);

        return (int) $query->fetchColumn();
    }


    //Supprimer les données d'un utilisateur, en gardant la ligne pour les commandes
    public function deleteUser(int $id): bool
    {
        $sql = "UPDATE users 
        SET first_name = 'Utilisateur',
            last_name = 'supprimé',
            email = :email,
            password = :password,
              is_active = 0
        WHERE id = :id";

        $query = $this->db->prepare($sql);

        return $query->execute([
            ":email" => "deleted-" . $id . "@wacdo.invalid",
            ":password" => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),// Mot de passe aléatoire que personne ne connaît pour éviter la connexion
            ":id" => $id
        ]);
    }
}


?>