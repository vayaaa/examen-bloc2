<?php

require_once __DIR__ . "/../config/config.php";

// Connexion à la bdd
$dsn = "mysql:host=127.0.0.1;port=8889;dbname=" . DB_DATABASE . ";charset=utf8mb4";
$db = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // les erreurs SQL arrivent dans le catch
]);

// Lecture des json
$categories = json_decode(file_get_contents(__DIR__ . "/data/categories.json"), true);
$catalog = json_decode(file_get_contents(__DIR__ . "/data/produits.json"), true);

if ($categories === null || $catalog === null) {
    die("Fichiers introuvables ou JSON invalide\n");
}

// On démarre la transaction : tout est enregistré à la fin, ou rien
$db->beginTransaction();

try {
    // Catégories
    $query = $db->prepare("INSERT INTO categories (name, image) VALUES (:name, :image)");
    $categoryIds = [];

    foreach ($categories as $category) {
        $query->execute([
            "name" => $category["title"],
            "image" => $category["image"],
        ]);

        $categoryIds[$category["title"]] = $db->lastInsertId();
    }

    // Produits (sauf menus)
    $query = $db->prepare("INSERT INTO products (category_id, name, price, image) VALUES (:category_id, :name, :price, :image)");
    $productIds = [];

    // $categoryName = "burgers", "boissons"... / $items = la liste de produits de cette catégorie
    foreach ($catalog as $categoryName => $items) {

        // Les menus ont leur propre table, on les fait plus bas
        if ($categoryName === "menus") {
            continue;
        }

        foreach ($items as $item) {
            $query->execute([
                "category_id" => $categoryIds[$categoryName],
                "name" => $item["nom"],
                "price" => $item["prix"],
                "image" => $item["image"],
            ]);

            $productIds[$item["nom"]] = $db->lastInsertId();
        }
    }

    // Menus
    $queryMenu = $db->prepare("INSERT INTO menus (name, price, image) VALUES (:name, :price, :image)");
    $queryLink = $db->prepare("INSERT INTO menu_products (menu_id, product_id, quantity) VALUES (:menu_id, :product_id, 1)");

    foreach ($catalog["menus"] as $menu) {
        $queryMenu->execute([
            "name" => $menu["nom"],
            "price" => $menu["prix"],
            "image" => $menu["image"],
        ]);

        $menuId = $db->lastInsertId();

        // Lien entre le menu et le burger : "Menu Big Mac" -> "Big Mac"
        $burgerName = str_replace("Menu ", "", $menu["nom"]);

        if (isset($productIds[$burgerName])) {
            $queryLink->execute([
                "menu_id" => $menuId,
                "product_id" => $productIds[$burgerName],
            ]);
        }
    }

    $db->commit(); // on valide tout

    echo "Import terminé : " . count($categoryIds) . " catégories, "
        . count($productIds) . " produits, " . count($catalog["menus"]) . " menus.\n";

} catch (Exception $e) {
    $db->rollBack(); // on annule tout
    echo "Erreur, rien n'a été importé : " . $e->getMessage() . "\n";
}

?>