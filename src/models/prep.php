<?php

class PrepModel
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

        $stats["to_prepare"] = $this->db->query("SELECT COUNT(*) FROM orders WHERE status = 'to_prepare'")->fetchColumn();
        $stats["prepared"] = $this->db->query("SELECT COUNT(*) FROM orders WHERE status = 'prepared'")->fetchColumn();
        $stats["delivered"] = $this->db->query("SELECT COUNT(*) FROM orders WHERE status = 'delivered'")->fetchColumn();

        return $stats;
    }

    //Récupérer la liste des commandes
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
        $status_sql = "UPDATE orders SET status = 'prepared'
        WHERE id = :id AND status = 'to_prepare'";

        $query = $this->db->prepare($status_sql);
        $query->bindValue(":id", $orderId);
        $query->execute();
    }


}

?>