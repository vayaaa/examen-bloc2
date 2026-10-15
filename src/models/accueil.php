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

    //Récupérer tous les menus disponibles
    public function getMenus(): array
    {
        $sql = "SELECT id, name, price, image
        FROM menus
        WHERE is_available = 1
        ORDER BY name";

        return $this->db->query($sql)->fetchAll();
    }




    // ---------- Petite méthode 1 : chercher UN produit ----------
    // "private" = utilisable seulement dans cette classe.
    // "array|false" = elle renvoie le produit (un tableau), ou false s'il n'existe pas.
    private function findProduct(int $id): array|false
    {
        // On récupère aussi le nom de la catégorie : on s'en servira pour vérifier les frites et les boissons.
        $sql = "SELECT products.id, products.name, products.price, categories.name AS category
                FROM products
                JOIN categories ON products.category_id = categories.id
                WHERE products.id = :id
                AND products.is_available = 1";

        $query = $this->db->prepare($sql);
        $query->bindValue(":id", $id);
        $query->execute();

        // fetch() = UNE ligne. Si aucune ligne ne correspond, il renvoie false.
        return $query->fetch();
    }


    // ---------- Petite méthode 2 : chercher UN menu ----------
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


    // ---------- Petite méthode 3 : préparer les lignes de la commande ----------
    // Elle vérifie tout, calcule le total, puis demande à la méthode 4 d'enregistrer.
    // $products  : [12 => 2, 15 => 1]  (id du produit => quantité)
    // $menuLines : [ ["id" => 3, "quantity" => 1, "side" => 36, "drink" => 27], ... ]
    public function newOrder(int $userId, string $source, string $deliveryTime, array $products, array $menuLines): string
    {
        $lines = [];  // la liste des lignes de la commande (vide au départ)
        $total = 0;   // le total (0 au départ)

        // ----- Les produits -----
        // $id = l'id du produit, $quantity = la quantité demandée
        foreach ($products as $id => $quantity) {

            $quantity = (int) $quantity; // (int) = on force un nombre entier

            if ($quantity <= 0) {
                continue; // quantité 0 : on passe au produit suivant
            }

            if ($quantity > 20) {
                throw new InvalidArgumentException("Quantité maximum : 20 par article.");
            }

            // On va chercher le produit dans la base : on ne fait pas confiance au formulaire
            $product = $this->findProduct((int) $id);

            if ($product === false) {
                throw new InvalidArgumentException("Produit introuvable ou indisponible.");
            }

            // [] = AJOUTE une ligne à la fin de la liste
            $lines[] = [
                "product_id" => $product["id"],
                "menu_id" => null,
                "name" => $product["name"],
                "quantity" => $quantity,
                "price" => $product["price"],  // le prix vient de la base
            ];

            $total = $total + $quantity * $product["price"];
        }

        // ----- Les menus -----
        foreach ($menuLines as $menuLine) {

            // Une ligne de menu doit être un tableau (id, quantity, side, drink)
            if (!is_array($menuLine)) {
                throw new InvalidArgumentException("Commande invalide.");
            }

            // "?? 0" = si la valeur n'existe pas, on prend 0
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

            // La frite et la boisson choisies : on les cherche dans la base aussi
            $side = $this->findProduct((int) ($menuLine["side"] ?? 0));
            $drink = $this->findProduct((int) ($menuLine["drink"] ?? 0));

            // Vérification : la frite existe ET est dans "frites", la boisson existe ET est dans "boissons"
            // || veut dire "ou" : si UNE seule de ces conditions est vraie, il y a un problème.
            if ($side === false || $side["category"] !== "frites" || $drink === false || $drink["category"] !== "boissons") {
                throw new InvalidArgumentException("Choisissez une frite et une boisson pour chaque menu.");
            }

            $lines[] = [
                "product_id" => null,
                "menu_id" => $menu["id"],
                // Le "." colle des textes ensemble : "Menu Big Mac (Grande Frite, Coca Cola)"
                "name" => $menu["name"] . " (" . $side["name"] . ", " . $drink["name"] . ")",
                "quantity" => $quantity,
                "price" => $menu["price"],
            ];

            $total = $total + $quantity * $menu["price"];
        }

        // ----- Une commande vide n'est pas une commande -----
        if (empty($lines)) {
            throw new InvalidArgumentException("Sélectionnez au moins un article.");
        }

        // Tout est bon : on enregistre (méthode 4) et on renvoie le numéro de ticket
        return $this->saveOrder($userId, $source, $deliveryTime, $lines, $total);
    }


    // ---------- Petite méthode 4 : enregistrer dans la base ----------
    private function saveOrder(int $userId, string $source, string $deliveryTime, array $lines, float $total): string
    {
        // Transaction = "tout ou rien" : on ne veut jamais une commande sans ses lignes.
        $this->db->beginTransaction();

        // try = "essaie". Si une erreur arrive, PHP saute dans catch.
        try {
            // 1. On crée la commande avec un ticket provisoire (le vrai ticket a besoin de l'id)
            $sql = "INSERT INTO orders (user_id, ticket_number, source, status, order_date, delivery_time, total_price)
                    VALUES (:user_id, :ticket, :source, 'to_prepare', NOW(), :delivery_time, :total)";

            $query = $this->db->prepare($sql);
            $query->execute([
                "user_id" => $userId,
                "ticket" => uniqid("TMP"),   // texte unique, par exemple TMP6710a3f2
                "source" => $source,
                "delivery_time" => $deliveryTime,
                "total" => round($total, 2), // 2 chiffres après la virgule
            ]);

            // 2. lastInsertId() = l'id que MySQL vient de donner à la commande
            $orderId = (int) $this->db->lastInsertId();

            // 3. Le vrai numéro de ticket : T pour téléphone, C pour comptoir
            $prefix = "C";

            if ($source === "telephone") {
                $prefix = "T";
            }

            $ticket = $prefix . (100 + $orderId); // par exemple C108

            $sql = "UPDATE orders SET ticket_number = :ticket WHERE id = :id";

            $query = $this->db->prepare($sql);
            $query->execute(["ticket" => $ticket, "id" => $orderId]);

            // 4. On enregistre chaque ligne de la commande
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

            // 5. Tout s'est bien passé : on valide
            $this->db->commit();

            return $ticket;

        } catch (Exception $e) {
            // Une erreur : on annule TOUT, comme si rien n'avait été fait
            $this->db->rollBack();

            // throw = on transmet l'erreur au contrôleur
            throw $e;
        }
    }
}

?>