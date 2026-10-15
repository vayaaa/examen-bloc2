<?php

class AdminModel
{
    private PDO $db;


    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    //Récupérer les infos de l'utilisateur connecté
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
    //Tableau de bord _________________________________________________________________
    public function getStats(): array
    {

        $stats = [];

        $stats["products"] = $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn();
        $stats["menus"] = $this->db->query("SELECT COUNT(*) FROM menus")->fetchColumn();
        $stats["users"] = $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats["order_to_prepare"] = $this->db->query("SELECT COUNT(*) FROM orders WHERE status = 'to_prepare'")->fetchColumn();

        return $stats;
    }


    //Produits_________________________________________________________________
    public function getProducts(): array
    {
        $products_sql = "SELECT products.id, products.name, products.price, products.description, products.is_available, products.image, categories.name AS category
                FROM products
                JOIN categories ON products.category_id = categories.id
                ORDER BY categories.name, products.name";

        $query = $this->db->prepare($products_sql);
        $query->execute();

        // Récupère tous les produits 
        $products = $query->fetchAll();

        foreach ($products as $key => $product) {
            // Correction lien image
            $products[$key]["image"] = str_replace(".png.png", ".png", $product["image"]);
            $products[$key]["image"] = str_replace(".jpg.png", ".png", $products[$key]["image"]);
        }

        return $products;
    }

    //Mise à jour des produits
    public function updateProduct(
        int $id,
        string $name,
        string $description,
        float $price,
        string $image,
        int $isAvailable
    ): bool {

        $sql = "UPDATE products
            SET name = :name,
            description = :description,
            price = :price,
            image = :image,
            is_available = :is_available
            WHERE id = :id";

        $query = $this->db->prepare($sql);

        $query->execute([
            ":name" => $name,
            ":description" => $description,
            ":price" => $price,
            ":image" => $image,
            ":is_available" => $isAvailable,
            ":id" => $id
        ]);

        // Vérifie que le produit existe
        if ($query->rowCount() === 0) {
            $check = $this->db->prepare(
                "SELECT id 
                FROM products 
                WHERE id = :id"
            );

            $check->execute([":id" => $id]);

            if ($check->fetch() === false) {
                return false;
            }
        }

        return true;
    }


    //Menus_________________________________________________________________
    // Récupérer tous les produits proposés comme options dans la modale de modif des menus
    public function getAvailableProducts(): array
    {
        $sql = "SELECT products.id, products.name, products.image, products.category_id
            FROM products
            JOIN categories ON products.category_id = categories.id
            WHERE categories.name IN ('frites', 'boissons')
            ORDER BY categories.name, products.name";

        $query = $this->db->prepare($sql);
        $query->execute();

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMenus(): array
    {
        $menus_sql = "SELECT menus.id, menus.name, menus.price, menus.is_available, menus.image
                FROM menus
                ORDER BY menus.name";

        $query = $this->db->prepare($menus_sql);
        $query->execute();

        $menus = $query->fetchAll(PDO::FETCH_ASSOC);

        //requête pour les options des menus
        $options_sql = "SELECT product_id
                    FROM menu_products
                    WHERE menu_id = :menu_id";

        $queryOptions = $this->db->prepare($options_sql);

        foreach ($menus as $key => $menu) {
            $queryOptions->execute([":menu_id" => $menu["id"]]);

            // Récupère les id des options
            $options = $queryOptions->fetchAll(PDO::FETCH_COLUMN);

            // JSON pour transmettre les options au js
            $menus[$key]["options"] = json_encode(
                array_map("intval", $options)
            );
        }

        return $menus;
    }

    //Mise à jour des menus
    public function updateMenu(
        int $id,
        string $name,
        string $description,
        float $price,
        string $image,
        int $isAvailable,
        array $options
    ): bool {

        try {
            $this->db->beginTransaction();

            $sql = "UPDATE menus
            SET name = :name,
            description = :description,
            price = :price,
            image = :image,
            is_available = :is_available
            WHERE id = :id";

            $query = $this->db->prepare($sql);

            $query->execute([
                ":name" => $name,
                ":description" => $description,
                ":price" => $price,
                ":image" => $image,
                ":is_available" => $isAvailable,
                ":id" => $id
            ]);

            // Vérifier que le menu existe 
            $check = $this->db->prepare(
                "SELECT id FROM menus WHERE id = :id"
            );


            $check->execute([":id" => $id]);

            if ($check->fetch() === false) {
                $this->db->rollBack();
                return false;
            }

            // Supprimer les anciennes associations du menu.
            $delete = $this->db->prepare(
                "DELETE FROM menu_products WHERE menu_id = :menu_id"
            );

            $delete->execute([
                ":menu_id" => $id
            ]);

            // Préparer la requête pour ajouter les nouvelles options.
            $insert = $this->db->prepare(
                "INSERT INTO menu_products (menu_id, product_id, quantity)
                 VALUES (:menu_id, :product_id, 1)"
            );

            // Enregistrer chaque option cochée.
            foreach ($options as $productId) {
                $insert->execute([
                    ":menu_id" => $id,
                    ":product_id" => $productId
                ]);
            }

            // Valider toutes les modifications.
            $this->db->commit();

            return true;


        } catch (PDOException $e) {
            // En cas d'erreur, annuler les modifications.
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            return false;

        }
    }



    //Utilisateurs_________________________________________________________________
    public function getUsers(): array
    {
        $users_sql = "SELECT users.id, users.first_name, users.last_name, users.email,  users.role_id, roles.name AS role
                FROM users
                JOIN roles ON users.role_id = roles.id
                ORDER BY users.last_name";

        $query = $this->db->prepare($users_sql);
        $query->execute();

        return $query->fetchAll();
    }

    //Mise à jour des utilisateurs
    public function updateUser(
        int $id,
        string $firstname,
        string $lastname,
        string $role,
    ): bool {

        $sql = "UPDATE users
            SET first_name = :firstname,
            last_name = :lastname,
            role_id = :role
            WHERE id = :id";

        $query = $this->db->prepare($sql);

        $query->execute([
            ":firstname" => $firstname,
            ":lastname" => $lastname,
            ":role" => $role,
            ":id" => $id
        ]);

        // Vérifie que le produit existe
        if ($query->rowCount() === 0) {
            $check = $this->db->prepare(
                "SELECT id 
                FROM users 
                WHERE id = :id"
            );

            $check->execute([":id" => $id]);

            // Si aucun utilisateur n'existe, la modification a échoué.
            if ($check->fetch() === false) {
                return false;
            }
        }

        return true;
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