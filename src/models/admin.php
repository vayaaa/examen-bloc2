<?php

class AdminModel
{
    private PDO $db;


    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    //Réupérer les infos de l'utilisateur connecté
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

    //Récupération des éléments à afficher dans le screen

    //Tableau de bord 
    public function getStats(): array
    {

        $stats = [];

        $stats["products"] = $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $stats["menus"] = $this->db->query("SELECT COUNT(*) FROM menus")->fetchColumn();
        $stats["users"] = $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats["order_to_prepare"] = $this->db->query("SELECT COUNT(*) FROM orders WHERE status = 'to_prepare'")->fetchColumn();

        return $stats;
    }


    //Produits
    public function getProducts(): array
    {
        $products_sql = "SELECT products.id, products.name, products.price, products.is_available, products.image, categories.name AS category
                FROM products
                JOIN categories ON products.category_id = categories.id
                ORDER BY categories.name, products.name";

        $query = $this->db->prepare($products_sql);
        $query->execute();

        // Récupère tous les produits 
        $products = $query->fetchAll();

        //Correction lien pour chaque img
        // Corrige les noms des images
        foreach ($products as $key => $product) {

            // Corrige .png.png en .png
            $products[$key]["image"] = str_replace(
                ".png.png",
                ".png",
                $product["image"]
            );

            // Corrige .jpg.png en .jpg
            $products[$key]["image"] = str_replace(
                ".jpg.png",
                ".png",
                $products[$key]["image"]
            );
        }

        return $products;
    }


    //Produits
    public function getMenus(): array
    {
        $menus_sql = "SELECT menus.id, menus.name, menus.price, menus.is_available, menus.image
                FROM menus
                ORDER BY menus.name";

        $query = $this->db->prepare($menus_sql);
        $query->execute();

        // Récupère tous les produits 
        return $query->fetchAll();
    }


    //Utilisateurs
    public function getUsers(): array
    {
        $users_sql = "SELECT users.id, users.first_name, users.last_name, users.email, roles.name AS role
                FROM users
                JOIN roles ON users.role_id = roles.id
                ORDER BY users.last_name";

        $query = $this->db->prepare($users_sql);
        $query->execute();

        return $query->fetchAll();
    }

    //Commandes 
    public function getOrders(): array
    {
        $orders_sql = "SELECT id, ticket_number, source, status, order_date, delivery_time, total_price
                FROM orders
                ORDER BY order_date";

        $query = $this->db->prepare($orders_sql);
        $query->execute();

        return $query->fetchAll();

    }
}

?>