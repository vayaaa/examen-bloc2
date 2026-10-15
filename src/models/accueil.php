<?php

class AccueilModel
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
    //Tableau de bord 
    public function getStats(): array
    {
        $stats = [];

        $stats["prepared"] = $this->db->query("SELECT COUNT(*) FROM orders WHERE status = 'prepared'")->fetchColumn();

        return $stats;
    }


    //Récupérer la liste des commandes préparées
    public function getOrders(string $status): array
    {
        $order_sql = "SELECT id, ticket_number, source, order_date, delivery_time
        FROM orders
        WHERE status = :status
        ORDER BY order_date ASC";

        $query = $this->db->prepare($order_sql);
        $query->bindValue(":status", $status);
        $query->execute();

        $orders = $query->fetchAll();

        foreach ($orders as $key => $order) {
            $orders[$key]["items"] = $this->getItems($order["id"]);
        }
        return $orders;
    }


    //Récupérer la liste des produits de chaque commande
    public function getItems(int $orderId): array
    {
        $item_sql = "SELECT name, quantity
        FROM order_items
        WHERE order_id = :order_id";

        $query = $this->db->prepare($item_sql);
        $query->bindValue(":order_id", $orderId);
        $query->execute();

        return $query->fetchAll();
    }


    //Changement de status (à préparer -> préparée)
    public function pushToPrepared(int $orderId): void
    {
        $status_sql = "UPDATE orders 
        SET status = 'prepared'
        WHERE id = :id AND status = 'to_prepare'";

        $query = $this->db->prepare($status_sql);
        $query->bindValue(":id", $orderId);
        $query->execute();
    }


    //Changement de status (préparée -> livrée)
    public function deliverOrder(int $orderId): void
    {
        $status_sql = "UPDATE orders 
        SET status = 'delivered'
        WHERE id = :id 
        AND status = 'prepared'";

        $query = $this->db->prepare($status_sql);
        $query->bindValue(":id", $orderId);
        $query->execute();
    }




    // Les catégories, dans l'ordre de la borne (menus, boissons, burgers...)
    public function getCategories(): array
    {
        $category_sql = "SELECT name, image 
        FROM categories 
        ORDER BY id";

        $query = $this->db->prepare($category_sql);
        $query->execute();

        // Récupère tous les produits 
        $category = $query->fetchAll();

        foreach ($category as $key) {
            //Correction lien image
            $key["image"] = str_replace(".png.png", ".png", $key["image"]);
            $key["image"] = str_replace(".jpg.png", ".png", $key["image"]);
        }
        return $category;
    }



    //Récupérer tous les produits disponibles
    public function getProducts(): array
    {
        $sql = "SELECT products.id, products.name, products.price, products.image,
        categories.name AS category
        FROM products
        JOIN categories ON products.category_id = categories.id
        WHERE products.is_available = 1
        ORDER BY categories.name, products.name";

        $rows = $this->db->query($sql)->fetchAll();

        // Chaque produit dans la liste de sa catégorie
        $grouped = [];

        foreach ($rows as $row) {
            //Correction lien image
            $row["image"] = str_replace(".png.png", ".png", $row["image"]);
            $row["image"] = str_replace(".jpg.png", ".png", $row["image"]);

            $grouped[$row["category"]][] = $row;
        }
        return $grouped;
    }

    //Récupérer tous les menus disponibles + options
    public function getMenus(): array
    {
        $sql = "SELECT id, name, price, image
        FROM menus
        WHERE is_available = 1
        ORDER BY name";


        $query = $this->db->prepare($sql);
        $query->execute();
        $menus = $query->fetchAll(PDO::FETCH_ASSOC);

        // Récupérer les produits associés à chaque menu
        $optionsSql = "SELECT product_id
                   FROM menu_products
                   WHERE menu_id = :menu_id";

        $queryOptions = $this->db->prepare($optionsSql);

        foreach ($menus as $key => $menu) {
            $queryOptions->execute([
                ":menu_id" => $menu["id"]
            ]);

            $options = $queryOptions->fetchAll(PDO::FETCH_COLUMN);

            $menus[$key]["options"] = json_encode(
                array_map("intval", $options)
            );
        }

        return $menus;
    }



    private function findProduct(int $id): array|false
    {
        $sql = "SELECT products.id, products.name, products.price, categories.name AS category
                FROM products
                JOIN categories ON products.category_id = categories.id
                WHERE products.id = :id
                AND products.is_available = 1";

        $query = $this->db->prepare($sql);
        $query->bindValue(":id", $id);
        $query->execute();

        return $query->fetch();
    }

    private function findMenu(int $id): array|false
    {
        $sql = "SELECT id, name, price
                FROM menus
                WHERE id = :id
                AND is_available = 1";

        $query = $this->db->prepare($sql);
        $query->bindValue(":id", $id);
        $query->execute();

        return $query->fetch();
    }

    public function newOrder(int $userId, string $source, string $deliveryTime, array $products, array $menuLines): string
    {
        $lines = [];
        $total = 0;


        foreach ($products as $id => $quantity) {

            $quantity = (int) $quantity;

            if ($quantity <= 0) {
                continue;
            }

            if ($quantity > 20) {
                throw new InvalidArgumentException("Quantité maximum : 20 par article.");
            }

            $product = $this->findProduct((int) $id);

            if ($product === false) {
                throw new InvalidArgumentException("Produit introuvable ou indisponible.");
            }


            $lines[] = [
                "product_id" => $product["id"],
                "menu_id" => null,
                "name" => $product["name"],
                "quantity" => $quantity,
                "price" => $product["price"],
            ];

            $total = $total + $quantity * $product["price"];
        }


        foreach ($menuLines as $menuLine) {

            if (!is_array($menuLine)) {
                throw new InvalidArgumentException("Commande invalide.");
            }

            $quantity = (int) ($menuLine["quantity"] ?? 0);

            if ($quantity <= 0) {
                continue;
            }

            if ($quantity > 20) {
                throw new InvalidArgumentException("Quantité maximum : 20 par article.");
            }

            $menu = $this->findMenu((int) ($menuLine["id"] ?? 0));

            if ($menu === false) {
                throw new InvalidArgumentException("Menu introuvable ou indisponible.");
            }

            $side = $this->findProduct((int) ($menuLine["side"] ?? 0));
            $drink = $this->findProduct((int) ($menuLine["drink"] ?? 0));


            if ($side === false || $side["category"] !== "frites" || $drink === false || $drink["category"] !== "boissons") {
                throw new InvalidArgumentException("Choisissez un accompagnement et une boisson");
            }

            $lines[] = [
                "product_id" => null,
                "menu_id" => $menu["id"],
                "name" => $menu["name"] . " (" . $side["name"] . ", " . $drink["name"] . ")",
                "quantity" => $quantity,
                "price" => $menu["price"],
            ];

            $total = $total + $quantity * $menu["price"];
        }

        if (empty($lines)) {
            throw new InvalidArgumentException("Sélectionnez au moins un article.");
        }

        return $this->saveOrder($userId, $source, $deliveryTime, $lines, $total);
    }


    private function saveOrder(int $userId, string $source, string $deliveryTime, array $lines, float $total): string
    {
        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO orders (user_id, ticket_number, source, status, order_date, delivery_time, total_price)
                    VALUES (:user_id, :ticket, :source, 'to_prepare', NOW(), :delivery_time, :total)";

            $query = $this->db->prepare($sql);
            $query->execute([
                "user_id" => $userId,
                "ticket" => uniqid("TMP"),
                "source" => $source,
                "delivery_time" => $deliveryTime,
                "total" => round($total, 2),
            ]);

            $orderId = (int) $this->db->lastInsertId();

            $prefix = "C";

            if ($source === "telephone") {
                $prefix = "T";
            }

            $ticket = $prefix . (100 + $orderId);

            $sql = "UPDATE orders SET ticket_number = :ticket WHERE id = :id";

            $query = $this->db->prepare($sql);
            $query->execute(["ticket" => $ticket, "id" => $orderId]);

            $sql = "INSERT INTO order_items (order_id, product_id, menu_id, name, quantity, price)
                    VALUES (:order_id, :product_id, :menu_id, :name, :quantity, :price)";

            $query = $this->db->prepare($sql);

            foreach ($lines as $line) {
                $query->execute([
                    "order_id" => $orderId,
                    "product_id" => $line["product_id"],
                    "menu_id" => $line["menu_id"],
                    "name" => $line["name"],
                    "quantity" => $line["quantity"],
                    "price" => $line["price"],
                ]);
            }

            $this->db->commit();

            return $ticket;

        } catch (Exception $e) {
            $this->db->rollBack();

            throw $e;
        }
    }
}

?>